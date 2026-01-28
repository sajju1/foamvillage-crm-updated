<?php

namespace App\Http\Controllers\Staff\Statement;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Services\Statement\CustomerStatementService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerStatementMail;


class CustomerStatementController extends Controller
{
    public function index()
    {
        $customers = Customer::query()
            ->orderBy('registered_company_name')
            ->orderBy('contact_name')
            ->get(['id', 'account_number', 'registered_company_name', 'contact_name']);

        return view('staff.statements.index', compact('customers'));
    }

    public function show(Request $request, Customer $customer, CustomerStatementService $service)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->string('from'))->startOfDay()
            : null;

        $to = $request->filled('to')
            ? Carbon::parse($request->string('to'))->endOfDay()
            : null;

        $statement = $service->build($customer, $from, $to);

        return view('staff.statements.show', [
            'customer'  => $customer,
            'statement' => $statement,
        ]);
    }


    public function pdf(Request $request, Customer $customer, CustomerStatementService $service)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->string('from'))->startOfDay()
            : null;

        $to = $request->filled('to')
            ? Carbon::parse($request->string('to'))->endOfDay()
            : null;

        $statement = $service->build($customer, $from, $to);

        return Pdf::loadView('staff.statements.pdf', [
            'customer'  => $customer,
            'statement' => $statement,
        ])
            ->setPaper('a4', 'landscape')
            ->download(
                'statement-' . $customer->account_number . '.pdf'
            );
    }

    public function email(Request $request, Customer $customer, CustomerStatementService $service)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->string('from'))->startOfDay()
            : null;

        $to = $request->filled('to')
            ? Carbon::parse($request->string('to'))->endOfDay()
            : null;

        $statement = $service->build($customer, $from, $to);

        Mail::to($customer->email)
            ->send(new CustomerStatementMail($customer, $statement));

        return back()->with('success', 'Statement emailed successfully.');
    }
}
