<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get("/users", [UserController::class, 'index']);

Route::get('/add-user', function () {
    return view('users.form');
});

Route::post('/add-user', [UserController::class, 'addUser'])->name('addUser');

Route::get('getUserData/{id}', [UserController::class, 'getUserData']);

Route::get('edit_update/{id}',function(){
    return view('users.edit_update');
});

Route::post('/updateUser/{id}', [UserController::class, 'update']);

Route::get('/add-student',    function(){
        return view('users.edit');
    });

// ===================StartProduct ===================
    
    Route::get('products', [ProductController::class, 'index'])->name('product-name');
    Route::get('product/create', [ProductController::class, 'create'])->name('product-create');
    Route::post('product/store', [ProductController::class, 'store'])->name('product-store');
    Route::post('product/update/{id}', [ProductController::class, 'update'])->name('product-update');
    Route::delete('product/delete/{id}', [ProductController::class, 'destroy'])->name('product-delete');
// ===================End Product ===================

Route::get('/reset-password', [UserController::class, 'resetPasswordLoad']);
Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('reset_password');