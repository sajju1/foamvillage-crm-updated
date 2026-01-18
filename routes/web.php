<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Company\CompanyBrandController;
use App\Http\Controllers\Company\DocumentDefaultController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductVariationController;
use App\Http\Controllers\Product\ProductPricingController;
use App\Http\Controllers\Product\CustomerPricingController;
use App\Http\Controllers\Product\ProductCategoryController;




Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

//--- Company routes

Route::middleware(['auth'])->prefix('companies')->group(function () {





    // Companies CRUD
    Route::get('/', [CompanyController::class, 'index'])->name('company.index');
    Route::get('/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/', [CompanyController::class, 'store'])->name('company.store');
    Route::get('/{company}', [CompanyController::class, 'show'])->name('company.show');
    Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/{company}', [CompanyController::class, 'update'])->name('company.update');

    // Company Brands
    Route::prefix('{company}/brands')->group(function () {
        Route::get('/', [CompanyBrandController::class, 'index'])->name('brands.index');
        Route::get('/create', [CompanyBrandController::class, 'create'])->name('brands.create');
        Route::post('/', [CompanyBrandController::class, 'store'])->name('brands.store');
        Route::get('/{brand}/edit', [CompanyBrandController::class, 'edit'])->name('brands.edit');
        Route::put('/{brand}', [CompanyBrandController::class, 'update'])->name('brands.update');
    });

    // Document Defaults
    Route::get(
        '{company}/document-defaults/{documentType?}',
        [DocumentDefaultController::class, 'edit']
    )->name('document-defaults.edit');

    Route::put(
        '{company}/document-defaults/{documentDefault}',
        [DocumentDefaultController::class, 'update']
    )->name('document-defaults.update');
});



Route::middleware(['auth'])->group(function () {

    /*
    |-------------------------
    | Products (Master)
    |-------------------------
    */
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');

    Route::get('/products/create', [ProductController::class, 'create'])
        ->name('products.create');

    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');

    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');

    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->name('products.update');


    /*
    |-------------------------
    | Product Variations
    |-------------------------
    */
    Route::get('/products/{product}/variations', [ProductVariationController::class, 'index'])
        ->name('products.variations.index');

    Route::get('/products/{product}/variations/create', [ProductVariationController::class, 'create'])
        ->name('products.variations.create');

    Route::post('/products/{product}/variations', [ProductVariationController::class, 'store'])
        ->name('products.variations.store');

    Route::get('/products/{product}/variations/{variation}/edit', [ProductVariationController::class, 'edit'])
        ->name('products.variations.edit');

    Route::put('/products/{product}/variations/{variation}', [ProductVariationController::class, 'update'])
        ->name('products.variations.update');


    /*
    |-------------------------
    | Product Pricing
    |-------------------------
    */

    // Product Options
    Route::get('/pricing/options', [ProductPricingController::class, 'options'])
        ->name('pricing.options');

    Route::post('/pricing/options', [ProductPricingController::class, 'storeOption'])
        ->name('pricing.options.store');

    // Foam Pricing Rules (per product)
    Route::get('/products/{product}/pricing/foam', [ProductPricingController::class, 'foamRules'])
        ->name('pricing.foam.index');

    Route::post('/products/{product}/pricing/foam', [ProductPricingController::class, 'storeFoamRule'])
        ->name('pricing.foam.store');


    /*
    |-------------------------
    | Customer Pricing
    |-------------------------
    */

    // Customer Product Portfolio
    Route::get('/customer-pricing/portfolio', [CustomerPricingController::class, 'portfolioIndex'])
        ->name('customer.pricing.portfolio');

    Route::post('/customer-pricing/portfolio', [CustomerPricingController::class, 'storePortfolio'])
        ->name('customer.pricing.portfolio.store');

    // Customer Discounts
    Route::get('/customer-pricing/discounts', [CustomerPricingController::class, 'discountsIndex'])
        ->name('customer.pricing.discounts');

    Route::post('/customer-pricing/discounts', [CustomerPricingController::class, 'storeDiscount'])
        ->name('customer.pricing.discounts.store');
});

Route::middleware(['auth'])->group(function () {

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

    /*
|--------------------------------------------------------------------------
| Product Variations (Variant-Based Products Only)
|--------------------------------------------------------------------------
*/

    Route::get(
        '/products/{product}/variations',
        [ProductVariationController::class, 'index']
    )->name('products.variations.index');

    Route::get(
        '/products/{product}/variations/create',
        [ProductVariationController::class, 'create']
    )->name('products.variations.create');

    Route::post(
        '/products/{product}/variations',
        [ProductVariationController::class, 'store']
    )->name('products.variations.store');

    Route::get(
        '/products/{product}/variations/{variation}/edit',
        [ProductVariationController::class, 'edit']
    )->name('products.variations.edit');

    Route::put(
        '/products/{product}/variations/{variation}',
        [ProductVariationController::class, 'update']
    )->name('products.variations.update');

    /*
|--------------------------------------------------------------------------
| Foam Pricing Rules (Rule-Based Products Only)
|--------------------------------------------------------------------------
*/

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

    /*
|--------------------------------------------------------------------------
| Foam Types (Calculatable Products)
|--------------------------------------------------------------------------
*/
    Route::get('/foam-types', [\App\Http\Controllers\Product\FoamTypeController::class, 'index'])
        ->name('foam-types.index');

    Route::get('/foam-types/create', [\App\Http\Controllers\Product\FoamTypeController::class, 'create'])
        ->name('foam-types.create');

    Route::post('/foam-types', [\App\Http\Controllers\Product\FoamTypeController::class, 'store'])
        ->name('foam-types.store');

    Route::get('/foam-types/{foamType}/edit', [\App\Http\Controllers\Product\FoamTypeController::class, 'edit'])
        ->name('foam-types.edit');

    Route::put('/foam-types/{foamType}', [\App\Http\Controllers\Product\FoamTypeController::class, 'update'])
        ->name('foam-types.update');
});
