<?php

namespace App\Http\Controllers\Staff\Payment;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Invoice\Invoice;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerPaymentController extends Controller
{
    /**
     * Payments index (CUSTOMER PAYMENTS ONLY).
     * Invoice-level payments are intentionally excluded.
     */
    public function index()
    {
        $payments = Payment::query()
            ->whereNull('invoice_id') // 🔒 customer payments only
            ->with(['allocations.invoice.customer'])
            ->orderByDesc('paid_at')
            ->paginate(20);

        return view('staff.payments.index', compact('payments'));
    }

    /**
     * Show create payment form (customer selectable).
     */
    public function create()
    {
        $customers = Customer::orderByRaw(
            "COALESCE(NULLIF(registered_company_name, ''), contact_name) ASC"
        )->get();

        return view('staff.payments.create', [
            'customers' => $customers,
            'customer'  => null,
            'invoices'  => collect(),
        ]);
    }

    /**
     * Show create payment form for a specific customer.
     */
    public function createForCustomer(Customer $customer)
    {
        $invoices = Invoice::query()
            ->where('customer_id', $customer->id)
            ->orderBy('issued_at')
            ->get()
            ->filter(fn ($invoice) => $invoice->balance_due > 0)
            ->values();

        return view('staff.payments.create', [
            'customer'  => $customer,
            'customers' => null,
            'invoices'  => $invoices,
        ]);
    }

    /**
     * Store a CUSTOMER payment and allocate it across invoices.
     *
     * IMPORTANT RULES:
     * - invoice_id MUST be null
     * - allocations drive invoice settlement
     * - Invoice module derives totals automatically
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'        => ['required', 'exists:customers,id'],
            'amount'             => ['required', 'numeric', 'min:0.01'],
            'currency'           => ['nullable', 'string', 'max:3'],
            'payment_method'     => ['required', 'string', 'max:50'],
            'payment_reference'  => ['nullable', 'string', 'max:100'],
            'paid_at'            => ['required', 'date'],
            'notes'              => ['nullable', 'string', 'max:500'],

            // allocations[invoice_id] => amount
            'allocations'        => ['nullable', 'array'],
            'allocations.*'      => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {

            /* ------------------------------------------------------------
             | Create CUSTOMER payment (no invoice_id by design)
             |------------------------------------------------------------ */
            $payment = Payment::create([
                'invoice_id'        => null,
                'amount'            => $data['amount'],
                'currency'          => $data['currency'] ?? 'GBP',
                'payment_method'    => $data['payment_method'],
                'payment_reference' => $data['payment_reference'] ?? null,
                'paid_at'           => $data['paid_at'],
                'notes'             => $data['notes'] ?? null,
            ]);

            $allocations = $data['allocations'] ?? [];

            /* ------------------------------------------------------------
             | 🔒 SNAPSHOT balances BEFORE allocation starts
             |------------------------------------------------------------ */
            $invoiceBalances = Invoice::query()
                ->whereIn('id', array_keys($allocations))
                ->lockForUpdate()
                ->get()
                ->mapWithKeys(function (Invoice $invoice) {
                    $paidViaInvoices = $invoice->payments()->sum('amount');
                    $paidViaCredits  = $invoice->creditAllocations()->sum('amount_applied');
                    $paidViaCustomer = $invoice->paymentAllocations()->sum('allocated_amount');

                    $balance = $invoice->total_amount
                        - $paidViaInvoices
                        - $paidViaCredits
                        - $paidViaCustomer;

                    return [$invoice->id => round($balance, 2)];
                });

            $allocatedTotal = 0;

            /* ------------------------------------------------------------
             | Allocation loop (uses snapshot ONLY)
             |------------------------------------------------------------ */
            foreach ($allocations as $invoiceId => $allocatedAmount) {

                if ($allocatedAmount <= 0) {
                    continue;
                }

                /** @var Invoice $invoice */
                $invoice = Invoice::findOrFail($invoiceId);

                if ($invoice->customer_id !== (int) $data['customer_id']) {
                    throw new \RuntimeException(
                        'Invoice does not belong to selected customer.'
                    );
                }

                $availableBalance = $invoiceBalances[$invoiceId] ?? 0;

                if ($allocatedAmount > $availableBalance) {
                    throw new \RuntimeException(
                        "Allocation exceeds balance due for invoice {$invoice->invoice_number}."
                    );
                }

                PaymentAllocation::create([
                    'payment_id'       => $payment->id,
                    'invoice_id'       => $invoice->id,
                    'allocated_amount' => $allocatedAmount,
                ]);

                $allocatedTotal += $allocatedAmount;
            }

            /* ------------------------------------------------------------
             | Allocation integrity check
             |------------------------------------------------------------ */
            if (round($allocatedTotal, 2) !== round($payment->amount, 2)) {
                throw new \RuntimeException(
                    'Allocated total must exactly match payment amount.'
                );
            }
        });

        return redirect()
            ->route('staff.payments.create')
            ->with('success', 'Customer payment recorded and allocated successfully.');
    }
}
