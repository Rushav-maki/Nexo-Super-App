<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\AgroController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\NexoPaisaController;
use App\Http\Controllers\CalendarController;


// ================== Public Routes ==================

// Front pages
Route::get('/', [FrontController::class, 'index']);
Route::get('/contact', [FrontController::class, 'contact']);
Route::get('/about', [FrontController::class, 'about']);
Route::post('/store_contact', [FrontController::class, 'store_contact'])->name('contact.store');

// ================== Guest Routes ==================
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// ================== Authenticated Routes ==================
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ================== User Routes ==================
    Route::middleware('role:user')->group(function () {
        Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
        Route::get('/user/profile', [UserController::class, 'profile'])->name('user.profile');

        // Travel Module
        Route::get('/travel', [TravelController::class, 'index'])->name('user.travel');
        Route::post('/plan', [TravelController::class, 'plan'])->name('user.travel.plan');
        Route::post('/hotels', [TravelController::class, 'hotels'])->name('user.travel.hotels');
        Route::post('/user/travel/cars', [TravelController::class, 'cars'])->name('user.travel.cars');
        Route::get('/travel/car-booking', [TravelController::class, 'carBooking'])->name('user.travel.carBooking');
        Route::post('/travel/car-payment', [TravelController::class, 'carPayment'])->name('user.travel.carPayment');
        Route::get('/travel/hotel-booking', [TravelController::class, 'hotelBooking'])->name('user.travel.hotelBooking');
        Route::post('/travel/hotel-payment', [TravelController::class, 'hotelPayment'])->name('user.travel.hotelPayment');

        // Agro Module
        Route::get('/agro', [AgroController::class, 'index'])->name('user.agro');
        Route::get('/agro/plants', [AgroController::class, 'getPlants'])->name('user.agro.plants');
        Route::get('/agro/plant/{id}', [AgroController::class, 'getPlantDetails'])->name('user.agro.plant');
        Route::get('/agro/translate', [AgroController::class, 'translatePlant'])->name('user.agro.translate');
        Route::get('/agro/diseases', [AgroController::class, 'getDiseases'])->name('user.agro.diseases');
        Route::post('/agro/analyze', [AgroController::class, 'analyze'])->name('user.agro.analyze');

        // Health Module
        Route::get('/health', [HealthController::class, 'index'])->name('user.health');
        Route::post('/health/check', [HealthController::class, 'check'])->name('user.health.check');
        Route::get('/health/travel-tips', [HealthController::class, 'getTravelTips'])->name('user.health.travel-tips');
        Route::get('/health/doctors', [HealthController::class, 'getDoctors'])->name('user.health.doctors');

        // Education Module
        Route::get('/education', [EducationController::class, 'index'])->name('user.education');
        Route::post('/education/generate', [EducationController::class, 'generateLesson'])->name('user.education.generate');
        Route::post('/education/followup', [EducationController::class, 'followUp'])->name('user.education.followup');
        Route::post('/education/quiz', [EducationController::class, 'quiz']);
        Route::post('/education/flash', [EducationController::class, 'flash']);


        // Chat Module
        Route::get('/chat', [ChatController::class, 'index'])->name('user.chat');
        Route::post('/chat/send', [ChatController::class, 'chat'])->name('user.chat.send');

        // Nexo Paisa
        Route::get('/load-nexo-paisa', [NexoPaisaController::class, 'load'])->name('user.loadNexoPaisa');
        Route::post('/load-nexo-paisa', [NexoPaisaController::class, 'loadPaisa'])->name('user.loadNexoPaisa.post');

        // Calendar Module
        Route::get('/calendar/{year?}', [CalendarController::class, 'index'])->name('user.calendar');
        Route::get('/api/todaydate', [CalendarController::class, 'todayDate']);
        Route::get('/api/bs-to-ad/{year}/{month}/{day}', [CalendarController::class, 'bsToAd']);
        Route::get('/api/ad-to-bs/{year}/{month}/{day}', [CalendarController::class, 'adToBs']);
        Route::get('/api/calculateage/bs/{year}/{month}/{day}', [CalendarController::class, 'calculateAgeFromBs']);
        Route::get('/api/calculateage/ad/{year}/{month}/{day}', [CalendarController::class, 'calculateAgeFromAd']);
    });
    // ================== Admin Routes ==================
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        // Add more admin routes here...
    });
});

// routes/web.php (temporary)
Route::get('/check-auth', fn() => auth()->check() ? 'LOGGED IN' : 'GUEST');

// Local debug route to show which OpenAI API key the web process sees (masked). Only enabled in local environment.
Route::get('/_debug/openai-key', function () {
    if (!app()->isLocal()) {
        abort(403);
    }
    $key = config('openai.api_key') ?: env('OPENAI_API_KEY');
    if (!$key) return response()->json(['key_masked' => '(none)']);
    $len = strlen($key);
    $masked = $len > 10 ? substr($key, 0, 6) . str_repeat('*', max(0, $len - 10)) . substr($key, -4) : $key;
    return response()->json(['key_masked' => $masked]);
});
