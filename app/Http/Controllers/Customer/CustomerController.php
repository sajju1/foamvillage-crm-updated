<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Company\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product\Product;


class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $companies = Company::orderBy('legal_name')->get();

        $companyId = $request->get(
            'company_id',
            Company::where('is_default', true)->value('id')
        );

        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status', '');

        $customersQuery = Customer::with('company')
            ->where('company_id', $companyId);

        // Status filter
        if (in_array($status, ['active', 'on_hold', 'blocked'], true)) {
            $customersQuery->where('customer_status', $status);
        }

        // Search
        if ($q !== '') {
            $customersQuery->where(function ($query) use ($q) {
                $query->where('account_number', 'like', "%{$q}%")
                    ->orWhere('contact_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('primary_phone', 'like', "%{$q}%")
                    ->orWhere('secondary_phone', 'like', "%{$q}%")
                    ->orWhere('registered_company_name', 'like', "%{$q}%")
                    ->orWhere('customer_registration_number', 'like', "%{$q}%")
                    ->orWhere('vat_number', 'like', "%{$q}%")
                    ->orWhereHas('addresses', function ($addr) use ($q) {
                        $addr->where('postcode', 'like', "%{$q}%")
                            ->where('is_active', true);
                    });
            });
        }

        $customers = $customersQuery
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('customers.index', compact(
            'customers',
            'companies',
            'companyId',
            'q',
            'status'
        ));
    }

    /**
     * Show the customer profile (read-first hub).
     */
    public function show(Customer $customer)
    {
        $customer->load([
            'company',
            'addresses' => function ($q) {
                $q->orderByDesc('is_default')
                    ->orderByDesc('created_at');
            },
        ]);

        $portfolio = $customer->portfolio()
            ->with(['product', 'variation'])
            ->orderByDesc('is_active')
            ->get();

        $activePortfolioMap = $portfolio
            ->where('is_active', true)
            ->mapWithKeys(function ($item) {
                $key = $item->product_variation_id
                    ? 'v_' . $item->product_variation_id
                    : 'p_' . $item->product_id;

                return [$key => true];
            });


        return view('customers.show', compact(
            'customer',
            'portfolio',
            'activePortfolioMap'
        ));
    }


    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $companies = Company::orderBy('legal_name')->get();
        $defaultCompanyId = Company::where('is_default', true)->value('id');

        return view('customers.create', compact(
            'companies',
            'defaultCompanyId'
        ));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id'                    => ['required', 'exists:companies,id'],
            'contact_name'                  => ['required', 'string', 'max:255'],
            'email'                         => ['required', 'email', 'max:255'],
            'primary_phone'                 => ['required', 'string', 'max:50'],
            'secondary_phone'               => ['nullable', 'string', 'max:50'],
            'registered_company_name'       => ['nullable', 'string', 'max:255'],
            'customer_registration_number'  => ['nullable', 'string', 'max:255'],
            'vat_number'                    => ['nullable', 'string', 'max:50'],
            'customer_status'               => ['required', 'in:active,on_hold,blocked'],
            'credit_limit'                  => ['nullable', 'numeric', 'min:0'],
            'payment_terms'                 => ['required', 'in:immediate,7_days,14_days,30_days,custom'],
            'internal_notes'                => ['nullable', 'string'],
            'account_manager_user_id'       => ['nullable', 'exists:users,id'],
        ]);

        $customer = DB::transaction(function () use ($validated) {
            return Customer::create(array_merge(
                $validated,
                [
                    'account_number' => $this->generateAccountNumber(),
                ]
            ));
        });

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Show the form for editing the specified customer (focused edit).
     */
    public function edit(Customer $customer)
    {
        $companies = Company::orderBy('legal_name')->get();

        return view('customers.edit', compact(
            'customer',
            'companies'
        ));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'company_id'                    => ['required', 'exists:companies,id'],
            'contact_name'                  => ['required', 'string', 'max:255'],
            'email'                         => ['required', 'email', 'max:255'],
            'primary_phone'                 => ['required', 'string', 'max:50'],
            'secondary_phone'               => ['nullable', 'string', 'max:50'],
            'registered_company_name'       => ['nullable', 'string', 'max:255'],
            'customer_registration_number'  => ['nullable', 'string', 'max:255'],
            'vat_number'                    => ['nullable', 'string', 'max:50'],
            'customer_status'               => ['required', 'in:active,on_hold,blocked'],
            'credit_limit'                  => ['nullable', 'numeric', 'min:0'],
            'payment_terms'                 => ['required', 'in:immediate,7_days,14_days,30_days,custom'],
            'internal_notes'                => ['nullable', 'string'],
            'account_manager_user_id'       => ['nullable', 'exists:users,id'],
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Generate a unique customer account number (5–6 digits).
     */
    protected function generateAccountNumber(): string
    {
        do {
            $number = (string) random_int(10000, 999999);
        } while (Customer::where('account_number', $number)->exists());

        return $number;
    }
}
