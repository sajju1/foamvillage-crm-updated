<?php

namespace App\Services\Statement;

use App\Models\Customer\Customer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerStatementService
{
    /**
     * Build a customer statement ledger between dates (inclusive).
     */
    public function build(Customer $customer, ?Carbon $from = null, ?Carbon $to = null): array
    {
        // Normalize date bounds (inclusive)
        $fromBound = $from?->copy()->startOfDay();
        $toBound   = $to?->copy()->endOfDay();

        $rows = collect()
            ->merge($this->invoiceRows($customer->id, $fromBound, $toBound))
            ->merge($this->paymentAllocationRows($customer->id, $fromBound, $toBound))
            ->merge($this->creditAllocationRows($customer->id, $fromBound, $toBound))
            ->sortBy(fn ($r) => $r['sort_key'])
            ->values();

        // Running balance
        $running = 0.0;
        $ledger = [];

        foreach ($rows as $r) {
            $debit  = (float) ($r['debit'] ?? 0);
            $credit = (float) ($r['credit'] ?? 0);

            $running = $running + $debit - $credit;

            $ledger[] = [
                'date'        => $r['date'],
                'reference'   => $r['reference'],
                'type'        => $r['type'],
                'description' => $r['description'],
                'debit'       => $debit,
                'credit'      => $credit,
                'balance'     => round($running, 2),
                'link'        => $r['link'] ?? null,
            ];
        }

        // Summary
        $openingBalance = 0.0;
        $totalInvoiced  = (float) collect($ledger)->sum('debit');
        $totalPaid      = (float) collect($ledger)->where('type', 'payment')->sum('credit');
        $totalCredits   = (float) collect($ledger)->where('type', 'credit')->sum('credit');
        $closingBalance = count($ledger)
            ? $ledger[array_key_last($ledger)]['balance']
            : $openingBalance;

        // Graph points (daily closing balance)
        $graphPoints = collect($ledger)
            ->groupBy(fn ($r) => $r['date']->format('Y-m-d'))
            ->map(fn ($rows) => [
                'date'    => $rows->last()['date']->format('Y-m-d'),
                'balance' => round($rows->last()['balance'], 2),
            ])
            ->values()
            ->toArray();

        return [
            'customer' => $customer,
            'from' => $fromBound,
            'to' => $toBound,
            'summary' => [
                'opening_balance' => $openingBalance,
                'total_invoiced'  => $totalInvoiced,
                'total_paid'      => $totalPaid,
                'total_credits'   => $totalCredits,
                'closing_balance' => $closingBalance,
            ],
            'rows' => $ledger,
            'graph_points' => $graphPoints,
        ];
    }

    private function invoiceRows(int $customerId, ?Carbon $from, ?Carbon $to): Collection
    {
        $q = DB::table('invoices')
            ->select(['id', 'invoice_number', 'issued_at'])
            ->where('customer_id', $customerId);

        if ($from) $q->where('issued_at', '>=', $from);
        if ($to)   $q->where('issued_at', '<=', $to);

        return collect($q->get())->map(function ($inv) {
            $date = Carbon::parse($inv->issued_at);

            // ✅ CORRECT: derive invoice value from invoice_lines
            $amount = DB::table('invoice_lines')
                ->where('invoice_id', $inv->id)
                ->sum('line_total_inc_vat');

            return [
                'sort_key'    => $date->timestamp . '_1_' . $inv->id,
                'date'        => $date,
                'reference'   => $inv->invoice_number,
                'type'        => 'invoice',
                'description' => 'Invoice ' . $inv->invoice_number,
                'debit'       => (float) $amount,
                'credit'      => 0.0,
                'link'        => route('staff.invoices.show', $inv->id),
            ];
        });
    }

    private function paymentAllocationRows(int $customerId, ?Carbon $from, ?Carbon $to): Collection
    {
        $q = DB::table('payment_allocations as pa')
            ->join('payments as p', 'p.id', '=', 'pa.payment_id')
            ->join('invoices as i', 'i.id', '=', 'pa.invoice_id')
            ->select([
                'pa.id as allocation_id',
                'pa.allocated_amount',
                'pa.created_at as allocated_at',
                'p.id as payment_id',
                'i.id as invoice_id',
                'i.invoice_number',
            ])
            ->where('i.customer_id', $customerId);

        if ($from) $q->where('pa.created_at', '>=', $from);
        if ($to)   $q->where('pa.created_at', '<=', $to);

        return collect($q->get())->map(function ($row) {
            $date = Carbon::parse($row->allocated_at);

            return [
                'sort_key'    => $date->timestamp . '_2_' . $row->allocation_id,
                'date'        => $date,
                'reference'   => 'PAY-' . str_pad($row->payment_id, 6, '0', STR_PAD_LEFT),
                'type'        => 'payment',
                'description' => 'Payment received (allocated to ' . $row->invoice_number . ')',
                'debit'       => 0.0,
                'credit'      => (float) $row->allocated_amount,
                'link'        => route('staff.invoices.show', $row->invoice_id),
            ];
        });
    }

    private function creditAllocationRows(int $customerId, ?Carbon $from, ?Carbon $to): Collection
    {
        $q = DB::table('credit_allocations as ca')
            ->join('credit_notes as cn', 'cn.id', '=', 'ca.credit_note_id')
            ->join('invoices as i', 'i.id', '=', 'ca.invoice_id')
            ->select([
                'ca.id as allocation_id',
                'ca.amount_applied',
                'ca.applied_at',
                'ca.notes',
                'cn.credit_note_number',
                'i.id as invoice_id',
                'i.invoice_number',
            ])
            ->where('cn.customer_id', $customerId);

        if ($from) $q->where('ca.applied_at', '>=', $from);
        if ($to)   $q->where('ca.applied_at', '<=', $to);

        return collect($q->get())->map(function ($row) {
            $date = Carbon::parse($row->applied_at);

            $desc = 'Credit note applied (' . $row->credit_note_number . ') to ' . $row->invoice_number;
            if (!empty($row->notes)) {
                $desc .= ' — ' . $row->notes;
            }

            return [
                'sort_key'    => $date->timestamp . '_3_' . $row->allocation_id,
                'date'        => $date,
                'reference'   => $row->credit_note_number,
                'type'        => 'credit',
                'description' => $desc,
                'debit'       => 0.0,
                'credit'      => (float) $row->amount_applied,
                'link'        => route('staff.invoices.show', $row->invoice_id),
            ];
        });
    }
}
