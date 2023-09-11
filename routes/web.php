<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OpsPajakReklameController;
use App\Http\Controllers\OpsSkbirtgsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

    /* [START] Branches */
    Route::get('/branches', [BranchController::class, 'index'])->name('branches');
    Route::post('/branches', [BranchController::class, 'import'])->name('branches.import');
    Route::put('/branches/{id}', [BranchController::class, 'update'])->name('branches.update');
    Route::delete('/branches/{id}', [BranchController::class, 'destroy'])->name('branches.delete');
    Route::get('/branches/export', [BranchController::class, 'export'])->name('branches.export');
    /* [END] Branches */

    /* [START] Employees */
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
    Route::post('/employees', [EmployeeController::class, 'import'])->name('employees.import');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.delete');
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    /* [END] Employees */

    Route::prefix('ops')->name('ops.')->group(function () {
        /* [START] Ops SKBIRTGS */
        Route::get('/skbirtgs', [OpsSkbirtgsController::class, 'index'])->name('skbirtgs');
        Route::post('/skbirtgs', [OpsSkbirtgsController::class, 'import'])->name('skbirtgs.import');
        Route::post('/skbirtgs/{id}', [OpsSkbirtgsController::class, 'upload'])->name('skbirtgs.upload');
        Route::put('/skbirtgs/{id}', [OpsSkbirtgsController::class, 'update'])->name('skbirtgs.update');
        Route::delete('/skbirtgs/{id}', [OpsSkbirtgsController::class, 'destroy'])->name('skbirtgs.delete');
        Route::get('/skbirtgs/export', [OpsSkbirtgsController::class, 'export'])->name('skbirtgs.export');
        /* [END] Ops SKBIRTGS */

        /* [START] Ops SK Operasional Cabang */

        /* [END] Ops SK Operasional Cabang */

        /* [START] Ops Pajak Reklame */
        Route::get('/pajak-reklame', [OpsPajakReklameController::class, 'index'])->name('pajak-reklame');
        Route::post('/pajak-reklame', [OpsPajakReklameController::class, 'import'])->name('pajak-reklame.import');
        Route::put('/pajak-reklame/{id}', [OpsPajakReklameController::class, 'update'])->name('pajak-reklame.update');
        Route::delete('/pajak-reklame/{id}', [OpsPajakReklameController::class, 'destroy'])->name('pajak-reklame.delete');
        /* [END] Ops Pajak Reklame */
    });
});

require __DIR__ . '/auth.php';
