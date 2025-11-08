<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Route publik: login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route yang harus login
Route::middleware(['auth'])->group(function () {
    // employee
    Route::get('/', [PageController::class, 'employeeShow'])->name('dashboard');
    Route::put('/employee-create', [PageController::class, 'employeeCreate'])->name('employee.create');
    Route::put('/employee-update/{id}', [PageController::class, 'employeeUpdate'])->name('employee.update');
    Route::get('/employee-delete/{id}', [PageController::class, 'employeeDelete'])->name('employee.delete');

    // office
    Route::get('/office', [PageController::class, 'officeShow'])->name('office');
    Route::put('/office-update/{id}', [PageController::class, 'officeUpdate'])->name('office.update');

    // report
    Route::get('/report', [PageController::class, 'showReport'])->name('report');
});
