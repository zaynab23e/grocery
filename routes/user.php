<?php

use Illuminate\Http\Request;
use App\Http\Controllers\user\AuthController;
use App\Http\Controllers\user\ProfileController;
use App\Http\Controllers\user\OrderController;
use App\Http\Controllers\user\ProductController;
use App\Http\Controllers\user\CategoryController;
use App\Http\Controllers\user\FavoriteProductsController;
use App\Http\Controllers\user\PaymentController;
use App\Http\Controllers\user\CartController;
use App\Http\Controllers\user\HomeController;
use App\Http\Controllers\user\RateController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


//authentication routes

Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    

    //profile routes
    Route::post('/logout', [AuthController::class, 'logout']); 
    Route::get('/user/profile/{id}', [ProfileController::class, 'getProfileUser']); 
    Route::post('/user/update-profile/{id}', [ProfileController::class, 'updateProfileUser']);
    Route::delete('/user/delete-account/{id}', [ProfileController::class, 'deleteAccountUser']); 
    Route::post('/user/change-password/{id}', [ProfileController::class, 'changePassword']); 

    // Orders 
    Route::get('/all_orders/{id}', [OrderController::class, 'index']); 
    Route::get('/orders/{id}', [OrderController::class, 'show']);  
    Route::post('/orders/{id}', [OrderController::class, 'store']);  
    Route::delete('/orders/{user_id}/{order_id}', [OrderController::class, 'destroy']);  
    // Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']); 
    // Route::post('orders/search', [OrderController::class, 'search']);
    
    //imageOrders 
    Route::post('/orders/{id}/images', [OrderController::class, 'uploadImages']);  
    Route::get('/orders/{id}/images', [OrderController::class, 'getImages']);  
    
    // Categories 
    Route::get('/categories/{id}', [CategoryController::class, 'index']);  
    Route::get('/categories/{category_id}/{user_id}', [CategoryController::class, 'show']); 
    // Route::post('/categories/search', [CategoryController::class, 'search']);
    // Route::post('/categories', [CategoryController::class, 'store']);  
    // Route::put('/categories/{id}', [CategoryController::class, 'update']);  
    // Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);  
    
    // Products 
    Route::get('/products', [ProductController::class, 'index']);  
    Route::get('/products/{id}', [ProductController::class, 'show']);  
    Route::post('/products', [ProductController::class, 'store']);  
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); 
    
    //imageProducts 
    Route::post('/products/{id}/images', [ProductController::class, 'uploadImages']);  
    Route::get('/products/{id}/images', [ProductController::class, 'getImages']);  

    // Favorite Products
    Route::get('/my_favorites/{id}', [FavoriteProductsController::class, 'index']);
    Route::post('/put_favorites', [FavoriteProductsController::class, 'store']);
    Route::delete('/favorites/{product_id}/{id}', [FavoriteProductsController::class, 'destroy']);

    // Cart
    Route::get('/cart/{user_id}', [CartController::class, 'index']); 
    Route::post('/cart/{user_id}', [CartController::class, 'store']); 
    Route::post('/cart/{Product_id}/{user_id}', [CartController::class, 'update']); 
    Route::delete('/cart/{product_id}/{user_id}', [CartController::class, 'destroy']); 
    Route::delete('/cart/{user_id}', [CartController::class, 'clearCart']); 

 //payment
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    // إرسال الدفع مباشرة (في حال حبيت تستدعيها يدويًا)
    Route::post('/payment/process', [PaymentController::class, 'paymentProcess'])->name('payment.process');
    // كول باك من Paymob بعد الدفع
    Route::post('/payment/callback', [PaymentController::class, 'callBack'])->name('payment.callback');
    // مسارات نجاح/فشل الدفع
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');

    //rate
    Route::post('/rate', [RateController::class, 'store']); 
    Route::get('/rate/{productId}', [RateController::class, 'index']); 
    Route::post('/update-rate/{product_Id}', [RateController::class, 'update']); 

    //home
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/search', [HomeController::class, 'search']);
    
    });

