<?php
use Illuminate\Http\Request;
use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\ProfileController;
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

Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


Route::group(['middleware' => ['auth:sanctum', 'auth:admin']], function () {
    Route::post('/logout', [AuthController::class, 'logout']); 
    Route::get('/profile/{id}', [ProfileController::class, 'getProfileAdmin']); 
    Route::post('/admin/update-profile/{id}', [ProfileController::class, 'updateProfileAdmin']); // Update admin profile
    Route::delete('/admin/delete-account/{id}', [ProfileController::class, 'deleteAccountAdmin']); // Delete admin account
    Route::post('/admin/change-password/{id}', [ProfileController::class, 'changePassword']); 
});
