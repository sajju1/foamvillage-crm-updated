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


/*
|--------------------------------------------------------------------------
| Orders
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Orders\OrderController;

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
| Customers (Module 04) — CLEAN & SAFE
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
    print
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
});

/*
|--------------------------------------------------------------------------
| Orders (Module 05)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('orders')->name('orders.')->group(function () {

    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/create', fn() => view('orders.create'))->name('create');
    Route::post('/', [OrderController::class, 'store'])->name('store');

    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
    Route::get('/{order}/print', [OrderController::class, 'print'])->name('print');

    Route::post('/{order}/lines', [OrderController::class, 'addLine'])->name('lines.add');
    Route::put('/{order}/lines/{line}', [OrderController::class, 'updateLine'])->name('lines.update');
    Route::delete('/{order}/lines/{line}', [OrderController::class, 'cancelLine'])->name('lines.cancel');

    Route::post('/{order}/submit', [OrderController::class, 'submit'])->name('submit');
});
