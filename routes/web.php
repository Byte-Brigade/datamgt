<?php

use App\Http\Controllers\BranchController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProfileController;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/branches', [BranchController::class, 'index'])->name('branches');
Route::post('/branches', [BranchController::class, 'importData'])->name('branches.import');
Route::get('/branches/export', [BranchController::class, 'exportData'])->name('branches.export');
Route::get('/home', [UsersController::class, 'index'])->name('home');
Route::post('/import', [UsersController::class, 'import'])->name('import');

Route::get('/employees', [BranchController::class, 'employeeIndex'])->name('employees');
Route::post('/employees', [BranchController::class, 'importEmployee'])->name('employees.import');

require __DIR__ . '/auth.php';
