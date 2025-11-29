<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\PricingController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\EsimPlanController;
use App\Http\Controllers\Frontend\Auth\LoginController;
use App\Http\Controllers\Frontend\Auth\RegisterController;
use App\Http\Controllers\Frontend\Auth\ForgotPasswordController;

use App\Http\Controllers\TestController;

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

Route::get('/detail/{id}', [EsimPlanController::class, 'detail'])->name('detail');

Route::get('/generate-qrcode', [KHqrController::class, 'generateQRCode'])->name('generate.qrcode');
Route::get('/checkQRCode', [KHqrController::class, 'checkTransactionByMD5'])->name('check.qrcode');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout/generate-qr', [CheckoutController::class, 'generateQr'])->name('checkout.generateQr');
    Route::post('/checkout/check-payment', [CheckoutController::class, 'checkPayment'])->name('checkout.checkPayment');
});
Route::post('/checkout/confirm', [CheckoutController::class, 'confirmPayment'])->name('checkout.confirm');

use App\Http\Controllers\TestResendController;

Route::get('/test-resend', [TestResendController::class, 'sendTestEmail']);

use App\Http\Controllers\TelegramController;

Route::get('/test-telegram', [TelegramController::class, 'sendTest']);

Route::get('/test', [TestController::class, 'test']);
