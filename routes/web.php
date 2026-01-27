<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Company
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Company\CompanyBrandController;
use App\Http\Controllers\Company\DocumentDefaultController;

/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductVariationController;
use App\Http\Controllers\Product\ProductPricingController;
use App\Http\Controllers\Product\ProductCategoryController;
use App\Http\Controllers\Product\FoamTypeController;

/*
|--------------------------------------------------------------------------
| Customers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\CustomerStatusController;
use App\Http\Controllers\Customer\CustomerAddressController;
use App\Http\Controllers\Customer\CustomerPortfolioController;
use App\Http\Controllers\Customer\CustomerPortfolioOfferController;
use App\Http\Controllers\Customer\CustomerPortfolioSheetController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\OrderSubmissionController;

/*
|--------------------------------------------------------------------------
| Delivery Notes (Staff)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Staff\DeliveryNoteController;
use App\Http\Controllers\Staff\Invoice\ConvertDeliveryNoteToInvoiceController;
use App\Http\Controllers\Staff\Vat\VatRuleController;
use App\Http\Controllers\Staff\Invoice\InvoiceController;
use App\Http\Controllers\Staff\Invoice\AccountCustomerPortfolioSearchController;
use App\Http\Controllers\Staff\Invoice\ProductVariationSearchController;



/*


|--------------------------------------------------------------------------
| Orders
|--------------------------------------------------------------------------
*/
// staff
use App\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\Http\Controllers\Staff\OrderAcknowledgementController;
use App\Http\Controllers\Staff\OrderLineController;

use App\Http\Controllers\Staff\Invoice\InvoicePdfController;
use App\Http\Controllers\Staff\Invoice\InvoiceEmailController;
use App\Http\Controllers\Staff\Invoice\UpdateInvoiceLineNoteController;
use App\Http\Controllers\Staff\Invoice\AddInvoiceLineController;




/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'));

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Auth Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Company Setup (Module 01)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('companies')->group(function () {

    Route::get('/', [CompanyController::class, 'index'])->name('company.index');
    Route::get('/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/', [CompanyController::class, 'store'])->name('company.store');
    Route::get('/{company}', [CompanyController::class, 'show'])->name('company.show');
    Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/{company}', [CompanyController::class, 'update'])->name('company.update');

    Route::prefix('{company}/brands')->group(function () {
        Route::get('/', [CompanyBrandController::class, 'index'])->name('brands.index');
        Route::get('/create', [CompanyBrandController::class, 'create'])->name('brands.create');
        Route::post('/', [CompanyBrandController::class, 'store'])->name('brands.store');
        Route::get('/{brand}/edit', [CompanyBrandController::class, 'edit'])->name('brands.edit');
        Route::put('/{brand}', [CompanyBrandController::class, 'update'])->name('brands.update');
    });

    Route::get(
        '{company}/document-defaults/{documentType?}',
        [DocumentDefaultController::class, 'edit']
    )->name('document-defaults.edit');

    Route::put(
        '{company}/document-defaults/{documentDefault}',
        [DocumentDefaultController::class, 'update']
    )->name('document-defaults.update');
});

/*
|--------------------------------------------------------------------------
| Products & Pricing (Module 03)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('products')->group(function () {

    Route::get('/product-categories', [ProductCategoryController::class, 'index'])
        ->name('product-categories.index');

    Route::get('/product-categories/create', [ProductCategoryController::class, 'create'])
        ->name('product-categories.create');

    Route::post('/product-categories', [ProductCategoryController::class, 'store'])
        ->name('product-categories.store');

    Route::get('/product-categories/{productCategory}/edit', [ProductCategoryController::class, 'edit'])
        ->name('product-categories.edit');

    Route::put('/product-categories/{productCategory}', [ProductCategoryController::class, 'update'])
        ->name('product-categories.update');

    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/', [ProductController::class, 'store'])->name('products.store');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');

    Route::get('/{product}/variations', [ProductVariationController::class, 'index'])
        ->name('products.variations.index');
    Route::get('/{product}/variations/create', [ProductVariationController::class, 'create'])
        ->name('products.variations.create');
    Route::post('/{product}/variations', [ProductVariationController::class, 'store'])
        ->name('products.variations.store');
    Route::get('/{product}/variations/{variation}/edit', [ProductVariationController::class, 'edit'])
        ->name('products.variations.edit');
    Route::put('/{product}/variations/{variation}', [ProductVariationController::class, 'update'])
        ->name('products.variations.update');

    Route::get(
        '/products/{product}/pricing/foam',
        [ProductPricingController::class, 'foamRules']
    )->name('pricing.foam.index');

    Route::get(
        '/products/{product}/pricing/foam/create',
        [ProductPricingController::class, 'createFoamRule']
    )->name('pricing.foam.create');

    Route::post(
        '/products/{product}/pricing/foam',
        [ProductPricingController::class, 'storeFoamRule']
    )->name('pricing.foam.store');

    Route::get(
        '/products/{product}/pricing/foam/{rule}/edit',
        [ProductPricingController::class, 'editFoamRule']
    )->name('pricing.foam.edit');

    Route::put(
        '/products/{product}/pricing/foam/{rule}',
        [ProductPricingController::class, 'updateFoamRule']
    )->name('pricing.foam.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/pricing/options', [ProductPricingController::class, 'options'])->name('pricing.options');
    Route::post('/pricing/options', [ProductPricingController::class, 'storeOption'])->name('pricing.options.store');

    Route::get('/foam-types', [FoamTypeController::class, 'index'])->name('foam-types.index');
    Route::get('/foam-types/create', [FoamTypeController::class, 'create'])->name('foam-types.create');
    Route::post('/foam-types', [FoamTypeController::class, 'store'])->name('foam-types.store');
    Route::get('/foam-types/{foamType}/edit', [FoamTypeController::class, 'edit'])->name('foam-types.edit');
    Route::put('/foam-types/{foamType}', [FoamTypeController::class, 'update'])->name('foam-types.update');
});

/*
|--------------------------------------------------------------------------
| Customers (Module 04)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('customers')->group(function () {

    Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/', [CustomerController::class, 'store'])->name('customers.store');

    Route::get('/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/{customer}', [CustomerController::class, 'update'])->name('customers.update');

    Route::put('/{customer}/status', [CustomerStatusController::class, 'update'])
        ->name('customers.status.update');

    /*
    | Addresses
    */
    Route::get('/{customer}/addresses/create', [CustomerAddressController::class, 'create'])
        ->name('customers.addresses.create');
    Route::post('/{customer}/addresses', [CustomerAddressController::class, 'store'])
        ->name('customers.addresses.store');
    Route::get('/addresses/{address}/edit', [CustomerAddressController::class, 'edit'])
        ->name('customers.addresses.edit');
    Route::put('/addresses/{address}', [CustomerAddressController::class, 'update'])
        ->name('customers.addresses.update');
    Route::delete('/addresses/{address}', [CustomerAddressController::class, 'deactivate'])
        ->name('customers.addresses.deactivate');

    /*
    | Portfolio
    */
    Route::post('/{customer}/portfolio', [CustomerPortfolioController::class, 'store'])
        ->name('customers.portfolio.store');
    Route::put('/portfolio/{portfolio}', [CustomerPortfolioController::class, 'update'])
        ->name('customers.portfolio.update');
    Route::delete('/portfolio/{portfolio}', [CustomerPortfolioController::class, 'deactivate'])
        ->name('customers.portfolio.deactivate');

    /*
    | Offers
    */
    Route::post('/portfolio/{portfolioEntry}/offers', [CustomerPortfolioOfferController::class, 'store'])
        ->name('customers.portfolio.offers.store');
    Route::delete('/portfolio/offers/{offer}', [CustomerPortfolioOfferController::class, 'deactivate'])
        ->name('customers.portfolio.offers.deactivate');

    /*
    | Portfolio Print
    */
    Route::get(
        '{customer}/portfolio/print',
        [CustomerPortfolioSheetController::class, 'print']
    )->name('customers.portfolio.print');

    Route::get(
        '{customer}/portfolio/pdf',
        [CustomerPortfolioSheetController::class, 'pdf']
    )->name('customers.portfolio.pdf');

    Route::get(
        '{customer}/portfolio/email',
        [CustomerPortfolioSheetController::class, 'email']
    )->name('customers.portfolio.email');

    /*
| Orders (Customer-scoped)
*/
    Route::prefix('{customer}')
        ->scopeBindings()
        ->group(function () {

            Route::get('orders', [CustomerOrderController::class, 'index'])
                ->name('orders.index');

            Route::get('orders/create', [CustomerOrderController::class, 'create'])
                ->name('orders.create');

            Route::get('orders/{order}', [CustomerOrderController::class, 'show'])
                ->name('orders.show');

            Route::get('orders/{order}/review', [CustomerOrderController::class, 'review'])
                ->name('orders.review');

            Route::post('orders/{order}/submit', [OrderSubmissionController::class, 'submit'])
                ->name('orders.submit');

            Route::get('orders/{order}/add-products', [CustomerOrderController::class, 'addProducts'])
                ->name('orders.add-products');

            Route::post('orders/{order}/add-products', [CustomerOrderController::class, 'storeAddedProducts'])
                ->name('orders.add-products.store');

            Route::post('orders/{order}/lines', [CustomerOrderController::class, 'upsertLine'])
                ->name('customers.orders.lines.upsert');

            Route::get('orders/{order}/print', [CustomerOrderController::class, 'print'])
                ->name('orders.print');
        });
});

/*
|--------------------------------------------------------------------------
| Orders (Module 05 - Staff)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {

    // Orders index
    Route::get('/orders', [StaffOrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/create', [StaffOrderController::class, 'create'])
        ->name('orders.create');

    Route::post('/orders', [StaffOrderController::class, 'store'])
        ->name('orders.store');

    Route::get('/orders/{order}', [StaffOrderController::class, 'show'])
        ->name('orders.show');

    Route::post('/orders/{order}/acknowledge', [OrderAcknowledgementController::class, 'acknowledge'])
        ->name('orders.acknowledge');

    Route::post('/orders/{order}/amend', [StaffOrderController::class, 'amend'])
        ->name('orders.amend');

    /*
|--------------------------------------------------------------------------
| Delivery / Collection Notes (Module 06)
|--------------------------------------------------------------------------
*/

    // Index
    Route::get('/delivery-notes', [DeliveryNoteController::class, 'index'])
        ->name('delivery-notes.index');

    // Create from order
    Route::get('/orders/{order}/delivery-notes/create', [DeliveryNoteController::class, 'create'])
        ->name('delivery-notes.create');

    Route::post('/orders/{order}/delivery-notes', [DeliveryNoteController::class, 'store'])
        ->name('delivery-notes.store');

    // Show / outputs
    Route::get('/delivery-notes/{deliveryNote}', [DeliveryNoteController::class, 'show'])
        ->name('delivery-notes.show');

    Route::get('/delivery-notes/{deliveryNote}/print', [DeliveryNoteController::class, 'print'])
        ->name('delivery-notes.print');

    Route::get('/delivery-notes/{deliveryNote}/pdf', [DeliveryNoteController::class, 'pdf'])
        ->name('delivery-notes.pdf');

    Route::post('/delivery-notes/{deliveryNote}/email', [DeliveryNoteController::class, 'email'])
        ->name('delivery-notes.email');


    Route::get(
        '/delivery-notes',
        [DeliveryNoteController::class, 'index']
    )->name('delivery-notes.index');

    Route::post(
        '/delivery-notes/{deliveryNote}/convert-to-invoice',
        ConvertDeliveryNoteToInvoiceController::class
    )->name('delivery-notes.convert-to-invoice');

    Route::get(
        '/invoices/{invoice}',
        [InvoiceController::class, 'show']
    )->name('invoices.show');



    Route::get('/invoices/{invoice}/print', [InvoicePdfController::class, 'show'])
        ->name('invoices.print');

    Route::get('/invoices/{invoice}/pdf', [InvoicePdfController::class, 'download'])
        ->name('invoices.pdf');

    Route::post('/invoices/{invoice}/email', InvoiceEmailController::class)
        ->name('invoices.email');

    Route::get('/invoices', [InvoiceController::class, 'index'])
        ->name('invoices.index');


    Route::patch(
        '/invoices/lines/{line}/note',
        [UpdateInvoiceLineNoteController::class, 'update']
    )->name('invoices.lines.note');

    Route::post(
        '/invoices/{invoice}/lines',
        [AddInvoiceLineController::class, 'store']
    )->name('invoices.lines.store');

    Route::get(
        '/invoices/search/portfolio/{customer}',
        AccountCustomerPortfolioSearchController::class
    )->name('invoices.search.portfolio');

    Route::get(
        '/invoices/search/products',
        ProductVariationSearchController::class
    )->name('invoices.search.products');

    Route::get(
        '/invoices/{invoice}/edit',
        [InvoiceController::class, 'edit']
    )->name('invoices.edit');

    Route::delete(
        '/invoices/lines/{line}',
        [AddInvoiceLineController::class, 'destroy']
    )->name('invoices.lines.destroy');
    Route::post(
        '/invoices/{invoice}/payments',
        [\App\Http\Controllers\Staff\Invoice\InvoicePaymentController::class, 'store']
    )->name('invoices.payments.store');
    Route::patch(
        '/invoices/{invoice}/due-date',
        [InvoiceController::class, 'updateDueDate']
    )->name('invoices.update-due-date');

    Route::post(
        '/invoices/{invoice}/send-reminder',
        [InvoiceController::class, 'sendReminder']
    )->name('invoices.send-reminder');
    Route::post(
        '/invoices/send-overdue-reminders',
        [InvoiceController::class, 'sendBulkOverdueReminders']
    )->name('invoices.send-overdue-reminders');



    Route::prefix('vat-rules')->name('vat-rules.')->group(function () {
        Route::get('/', [VatRuleController::class, 'index'])->name('index');
        Route::post('/', [VatRuleController::class, 'store'])->name('store');
        Route::patch('{vatRule}/deactivate', [VatRuleController::class, 'deactivate'])
            ->name('deactivate');
    });




    /*
    |--------------------------------------------------------------------------
    | Order Line Operations
    |--------------------------------------------------------------------------
    */

    Route::post('/order-lines/{orderLine}/processed-quantity', [OrderLineController::class, 'updateProcessedQuantity'])
        ->name('order-lines.processed-quantity');

    Route::post('/order-lines/{orderLine}/cancel', [OrderLineController::class, 'cancel'])
        ->name('order-lines.cancel');
});
