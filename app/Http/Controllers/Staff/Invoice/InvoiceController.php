<?php

namespace App\Http\Controllers\Staff\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice\Invoice;
use App\Models\Company\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoicePaymentReminderMail;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::query()->with('customer');

        /* ================= SEARCH ================= */
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($c) use ($search) {
                        $c->where('account_number', 'like', "%{$search}%")
                            ->orWhere('contact_name', 'like', "%{$search}%")
                            ->orWhere('registered_company_name', 'like', "%{$search}%");
                    });
            });
        }

        /* ================= STATUS FILTER ================= */
        if ($status = $request->input('status')) {

            if ($status === 'paid') {
                $query->whereRaw('
                    (select coalesce(sum(amount),0)
                     from payments
                     where payments.invoice_id = invoices.id
                    ) >= invoices.total_amount
                ');
            }

            if ($status === 'due') {
                $query->where(function ($q) {
                    $q->whereDate('due_date', '>=', Carbon::today())
                        ->orWhereNull('due_date');
                })->whereRaw('
                    (select coalesce(sum(amount),0)
                     from payments
                     where payments.invoice_id = invoices.id
                    ) < invoices.total_amount
                ');
            }

            if ($status === 'overdue') {
                $query->whereDate('due_date', '<', Carbon::today())
                    ->whereRaw('
                        (select coalesce(sum(amount),0)
                         from payments
                         where payments.invoice_id = invoices.id
                        ) < invoices.total_amount
                    ');
            }
        }

        /* ================= BADGE COUNTS ================= */
        $today = Carbon::today();

        $overdueCount = (clone $query)
            ->whereDate('due_date', '<', $today)
            ->whereRaw('
                total_amount > (
                    select coalesce(sum(amount),0)
                    from payments
                    where payments.invoice_id = invoices.id
                )
            ')
            ->count();

        $dueSoonCount = (clone $query)
            ->whereBetween('due_date', [$today, $today->copy()->addDays(7)])
            ->whereRaw('
                total_amount > (
                    select coalesce(sum(amount),0)
                    from payments
                    where payments.invoice_id = invoices.id
                )
            ')
            ->count();

        $invoices = $query
            ->latest('issued_at')
            ->paginate(20)
            ->withQueryString();

        return view(
            'staff.invoices.index',
            compact('invoices', 'overdueCount', 'dueSoonCount')
        );
    }

    public function show(Invoice $invoice)
    {
        $invoice->load([
            'lines',
            'customer',
            'deliveryNote',
            'payments',
            'creditAllocations',
        ]);

        $company = Company::first();

        return view('staff.invoices.show', compact('invoice', 'company'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->balance_due <= 0) {
            abort(403, 'This invoice is fully paid and cannot be edited.');
        }

        $invoice->load(['lines', 'customer']);
        $company = Company::first();

        return view('staff.invoices.edit', compact('invoice', 'company'));
    }

    public function updateDueDate(Request $request, Invoice $invoice)
    {
        if ($invoice->balance_due <= 0) {
            abort(403, 'Paid invoices cannot be modified.');
        }

        $validated = $request->validate([
            'due_date' => ['required', 'date'],
        ]);

        $invoice->update([
            'due_date' => $validated['due_date'],
        ]);

        return back()->with('success', 'Invoice due date updated.');
    }

    /* ================= EMAIL REMINDERS ================= */

    public function sendReminder(Invoice $invoice)
    {
        if ($invoice->balance_due <= 0) {
            return back()->with('error', 'This invoice is already paid.');
        }


        if (!$invoice->customer?->email) {
            return back()->with('error', 'Customer has no email address.');
        }
        if (
            $invoice->last_reminded_at &&
            $invoice->last_reminded_at->gt(now()->subDay())
        ) {
            return back()->with('error', 'A reminder was already sent recently.');
        }

        Mail::to($invoice->customer->email)
            ->send(new InvoicePaymentReminderMail($invoice));

        $invoice->update([
            'last_reminded_at' => now(),
        ]);


        return back()->with('success', 'Payment reminder sent.');
    }

    public function sendBulkOverdueReminders()
    {
        $today = Carbon::today();

        $invoices = Invoice::with('customer')
            ->whereDate('due_date', '<', $today)
            ->whereRaw('
                total_amount > (
                    select coalesce(sum(amount),0)
                    from payments
                    where payments.invoice_id = invoices.id
                )
            ')
            ->get();

        foreach ($invoices as $invoice) {

            if (!$invoice->customer?->email) {
                continue;
            }

            if (
                $invoice->last_reminded_at &&
                $invoice->last_reminded_at->gt(now()->subDay())
            ) {
                continue; // skip recently reminded invoices
            }

            Mail::to($invoice->customer->email)
                ->send(new InvoicePaymentReminderMail($invoice));

            $invoice->update([
                'last_reminded_at' => now(),
            ]);
        }


        return back()->with(
            'success',
            $invoices->count() . ' overdue reminder(s) sent.'
        );
    }
}
