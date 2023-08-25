<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OpsSkbirtgsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
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
    Route::get('/branches/export', [BranchController::class, 'export'])->name('branches.export');
    /* [END] Branches */

    /* [START] Employees */
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
    Route::post('/employees', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    /* [END] Employees */

    Route::prefix('ops')->name('ops.')->group(function () {
        /* [START] Ops SKBIRTGS */
        Route::get('/skbirtgs', [OpsSkbirtgsController::class, 'index'])->name('skbirtgs');
        Route::post('/skbirtgs', [OpsSkbirtgsController::class, 'import'])->name('skbirtgs.import');
        /* [END] Ops SKBIRTGS */
    });
});

Route::get('/test', [BranchController::class, 'testApi'])->name('test');
Route::get('/home', [UsersController::class, 'index'])->name('home');
Route::post('/import', [UsersController::class, 'import'])->name('import');


require __DIR__ . '/auth.php';
