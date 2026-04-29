<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\SuperAdminMiddleware;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');

Route::post('/api/sensor-data', [SensorController::class, 'store']);
Route::get('/api/realtime', [SensorController::class, 'getRealtimeData']);

Route::middleware(['auth', SuperAdminMiddleware::class])->group(function () {
    Route::get('/', [SensorController::class, 'index'])->name('dashboard');
    Route::get('/export_sensor', [SensorController::class, 'exportSensor'])->name('export_sensor');
    Route::resource('users', UserController::class);
    Route::get('getallusers', [UserController::class, 'getAllUsers'])->name('getallusers');
    Route::get('log-sensor', [SensorController::class, 'log_sensor'])->name('log_sensor');
    Route::get('data-sensor', [SensorController::class, 'data_sensor'])->name('data_sensor');
    route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
