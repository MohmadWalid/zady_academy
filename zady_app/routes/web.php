<?php

use App\Http\Controllers\Auth\AccessCodeController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Secretary;
use App\Http\Controllers\Teacher;
use App\Http\Controllers\ParentRole;
use App\Http\Controllers\ProofImageController;
use Illuminate\Support\Facades\Route;

// ── Root redirect ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->dashboardRoute());
    }
    return redirect()->route('login');
});

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::get('/login',  [AccessCodeController::class, 'showForm'])->name('login');
Route::post('/login', [AccessCodeController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [AccessCodeController::class, 'logout'])->name('logout')->middleware('auth');

// ── Proofs ────────────────────────────────────────────────────────────────────
Route::get('/proofs/{payment_code}', [ProofImageController::class, 'show'])->name('proofs.show')->middleware('auth');

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/pending',   [Admin\PaymentController::class, 'pending'])->name('pending');
        Route::get('/cash',      [Admin\PaymentController::class, 'cash'])->name('cash');
        Route::post('/cash',     [Admin\PaymentController::class, 'store_cash'])->name('store_cash');
        Route::get('/history',   [Admin\PaymentController::class, 'history'])->name('history');
        Route::get('/transfers', [Admin\PaymentController::class, 'transfers'])->name('transfers');
        
        Route::post('/{payment}/approve', [Admin\PaymentController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject',  [Admin\PaymentController::class, 'reject'])->name('reject');
        Route::post('/{payment}/refund',  [Admin\PaymentController::class, 'refund'])->name('refund');
    });

    // Academic
    Route::prefix('academic')->name('academic.')->group(function () {
        Route::resource('students',     Admin\StudentController::class);
        Route::post('students/{student}/restore', [Admin\StudentController::class, 'restore'])->name('students.restore')->withTrashed();

        Route::resource('groups',       Admin\GroupController::class);
        Route::post('groups/{group}/restore', [Admin\GroupController::class, 'restore'])->name('groups.restore')->withTrashed();
        Route::post('groups/{group}/sessions', [Admin\GroupController::class, 'addSession'])->name('groups.sessions.store');
        Route::delete('groups/{group}/sessions/{session}', [Admin\GroupController::class, 'removeSession'])->name('groups.sessions.destroy');

        Route::resource('users',        Admin\UserController::class);
        Route::post('users/{user}/restore', [Admin\UserController::class, 'restore'])->name('users.restore')->withTrashed();

        Route::resource('subscriptions', Admin\SubscriptionController::class);
        Route::post('subscriptions/generate', [Admin\SubscriptionController::class, 'generate'])->name('subscriptions.generate');
        Route::post('subscriptions/{subscription}/restore', [Admin\SubscriptionController::class, 'restore'])->name('subscriptions.restore')->withTrashed();

        Route::resource('enrollments', Admin\EnrollmentController::class)->only(['store', 'destroy']);
    });

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/',        [Admin\AttendanceController::class, 'index'])->name('index');
        Route::get('/{group}', [Admin\AttendanceController::class, 'show'])->name('show');
        Route::post('/{group}', [Admin\AttendanceController::class, 'store'])->name('store');
    });
});

// ── Secretary ─────────────────────────────────────────────────────────────────
Route::prefix('secretary')->name('secretary.')->middleware(['auth', 'role:secretary'])->group(function () {

    Route::get('/dashboard', [Secretary\DashboardController::class, 'index'])->name('dashboard');

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/pending', [Secretary\PaymentController::class, 'pending'])->name('pending');
        Route::get('/cash',    [Secretary\PaymentController::class, 'cash'])->name('cash');
        Route::post('/cash',   [Secretary\PaymentController::class, 'store_cash'])->name('store_cash');
        Route::get('/history', [Secretary\PaymentController::class, 'history'])->name('history');
        
        Route::post('/{payment}/approve', [Secretary\PaymentController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject',  [Secretary\PaymentController::class, 'reject'])->name('reject');
    });

    // Academic
    Route::prefix('academic')->name('academic.')->group(function () {
        Route::resource('students',      Secretary\StudentController::class);
        Route::post('students/{student}/restore', [Secretary\StudentController::class, 'restore'])->name('students.restore')->withTrashed();
        Route::resource('groups',        Secretary\GroupController::class);
        Route::resource('users',         Secretary\UserController::class);
        Route::resource('subscriptions', Secretary\SubscriptionController::class);
        Route::resource('enrollments',   Secretary\EnrollmentController::class)->only(['store', 'destroy']);
    });

    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/',        [Secretary\AttendanceController::class, 'index'])->name('index');
        Route::get('/{group}', [Secretary\AttendanceController::class, 'show'])->name('show');
        Route::post('/{group}', [Secretary\AttendanceController::class, 'store'])->name('store');
    });
});

// ── Teacher ───────────────────────────────────────────────────────────────────
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher'])->group(function () {

    Route::get('/dashboard', [Teacher\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/',        [Teacher\AttendanceController::class, 'index'])->name('index');
        Route::get('/{group}', [Teacher\AttendanceController::class, 'show'])->name('show');
        Route::post('/{group}', [Teacher\AttendanceController::class, 'store'])->name('store');
    });
});

// ── Parent ────────────────────────────────────────────────────────────────────
Route::prefix('parent')->name('parent.')->middleware(['auth', 'role:parent'])->group(function () {

    Route::get('/dashboard', [ParentRole\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('children')->name('children.')->group(function () {
        Route::get('/', [ParentRole\ChildrenController::class, 'index'])->name('index');
    });

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/',       [ParentRole\PaymentController::class, 'index'])->name('index');
        Route::get('/upload', [ParentRole\PaymentController::class, 'upload'])->name('upload');
        Route::post('/upload', [ParentRole\PaymentController::class, 'store'])->name('store');
    });
});
