<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\PricingController;
use App\Http\Controllers\Frontend\Auth\LoginController;
use App\Http\Controllers\Frontend\Auth\RegisterController;
use App\Http\Controllers\Frontend\Auth\ForgotPasswordController;

use App\Http\Controllers\Frontend\KHqrController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

Route::get('/profile', function() {
    return view('frontend.profile.index');
})->name('profile');

Route::controller(CartController::class)
    ->prefix('cart')
    ->as('cart.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add', 'add')->name('add');
        Route::delete('/remove/{id}', 'remove')->name('remove');
        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
    });


Route::get('/about', function() {
    return view('frontend.about.index');
})->name('about');

Route::get('/auth/login', function() {
    return view('frontend.auth.login');
})->name('auth.login');

Route::get('/generate-qrcode', [KHqrController::class, 'generateQRCode'])->name('generate.qrcode');
Route::get('/checkQRCode', [KHqrController::class, 'checkTransactionByMD5'])->name('check.qrcode');
