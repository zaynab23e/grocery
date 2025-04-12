<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\DashboardController;


    // المسارات الحالية
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/import/categories', [DashboardController::class, 'importCategories'])->name('admin.import.categories');
    Route::post('/import/products', [DashboardController::class, 'importProducts'])->name('admin.import.products');
    
    // المسارات الجديدة
    Route::get('/categories/{category}/products', [DashboardController::class, 'getCategoryProducts'])
    ->name('admin.categories.products');
    Route::get('/products/{id}', [DashboardController::class, 'showProduct'])->name('admin.products.show');
    Route::delete('/products/{id}', [DashboardController::class, 'destroyProduct'])->name('admin.products.destroy');
    Route::get('/products/{id}/edit', [DashboardController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/products/{id}', [DashboardController::class, 'updateProduct'])->name('admin.products.update');
    Route::post('/products/{id}/update-image', [DashboardController::class, 'updateProductImage'])->name('admin.products.update-image');
