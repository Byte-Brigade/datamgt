<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InqueryController;
use App\Http\Controllers\OpsAparController;
use App\Http\Controllers\OpsPajakReklameController;
use App\Http\Controllers\OpsSkbirtgsController;
use App\Http\Controllers\OpsSkOperasionalController;
use App\Http\Controllers\OpsSpecimentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UAMController;
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

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
});


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('/inquery')->group(function () {
        Route::get('/branch', [InqueryController::class, 'branch'])->name('inquery.branch');
        Route::get('/branch/{id}', [InqueryController::class, 'branchDetail'])->name('inquery.branch.detail');
    });

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

    Route::middleware('role:superadmin')->group(function () {
        /* [START] User Access Management */
        Route::get('/uam', [UAMController::class, 'index'])->name('uam');
        Route::post('/uam', [UAMController::class, 'store'])->name('uam.store');
        Route::put('/uam/{id}', [UAMController::class, 'index'])->name('uam.update');
        Route::delete('/uam/{id}', [UAMController::class, 'index'])->name('uam.delete');
        /* [END] User Access Management*/
    });

    Route::prefix('ops')->name('ops.')->group(function () {

        /* [START] Ops SKBIRTGS */
        Route::get('/skbirtgs', [OpsSkbirtgsController::class, 'index'])->name('skbirtgs');
        Route::post('/skbirtgs', [OpsSkbirtgsController::class, 'import'])->name('skbirtgs.import');
        Route::post('/skbirtgs/{id}', [OpsSkbirtgsController::class, 'upload'])->name('skbirtgs.upload');
        Route::get('/skbirtgs/export', [OpsSkbirtgsController::class, 'export'])->name('skbirtgs.export');
        /* [END] Ops SKBIRTGS */

        /* [START] Ops SK Operasional Cabang */
        Route::get('/sk-operasional', [OpsSkOperasionalController::class, 'index'])->name('sk-operasional');
        Route::post('/sk-operasional', [OpsSkOperasionalController::class, 'import'])->name('sk-operasional.import');
        Route::post('/sk-operasional/{id}', [OpsSkOperasionalController::class, 'upload'])->name('sk-operasional.upload');
        Route::get('/sk-operasional/export', [OpsSkOperasionalController::class, 'export'])->name('sk-operasional.export');
        /* [END] Ops SK Operasional Cabang */

        /* [START] Ops Pajak Reklame */
        Route::get('/pajak-reklame', [OpsPajakReklameController::class, 'index'])->name('pajak-reklame');
        Route::post('/pajak-reklame', [OpsPajakReklameController::class, 'import'])->name('pajak-reklame.import');
        Route::post('/pajak-reklame/{id}', [OpsPajakReklameController::class, 'upload'])->name('pajak-reklame.upload');
        Route::get('/pajak-reklame/export', [OpsPajakReklameController::class, 'export'])->name('pajak-reklame.export');
        /* [END] Ops Pajak Reklame */


        /* [START] Ops Speciment */
        Route::get('/speciment', [OpsSpecimentController::class, 'index'])->name('speciment');
        Route::post('/speciment', [OpsSpecimentController::class, 'import'])->name('speciment.import');
        Route::post('/speciment/{id}', [OpsSpecimentController::class, 'upload'])->name('speciment.upload');
        Route::get('/speciment/export', [OpsSpecimentController::class, 'export'])->name('speciment.export');
        /* [END] Ops Speciment */


        /* [START] Ops APAR */
        Route::get('/apar', [OpsAparController::class, 'index'])->name('apar');
        Route::post('/apar/import', [OpsAparController::class, 'import'])->name('apar.import');
        Route::post('/apar', [OpsAparController::class, 'store'])->name('apar.store');


        Route::get('/apar/detail/{id}', [OpsAparController::class, 'detail'])->name('apar.detail');
        Route::get('/apar/export', [OpsAparController::class, 'export'])->name('apar.export');
        /* [END] Ops APAR */

        Route::group(['middleware' => ['role:branch-ops|superadmin']], function () {
            Route::group(['middleware' => ['permission:can edit']], function () {
                Route::put('/skbirtgs/{id}', [OpsSkbirtgsController::class, 'update'])->name('skbirtgs.update');
                Route::put('/sk-operasional/{id}', [OpsSkOperasionalController::class, 'update'])->name('sk-operasional.update');
                Route::put('/apar/{id}', [OpsAparController::class, 'update'])->name('apar.update');
                Route::put('/apar/detail/{id}', [OpsAparController::class, 'update_detail'])->name('apar.detail.update');
                Route::put('/pajak-reklame/{id}', [OpsPajakReklameController::class, 'update'])->name('pajak-reklame.update');
                Route::put('/speciment/{id}', [OpsSpecimentController::class, 'update'])->name('speciment.update');
            });
            Route::group(['middleware' => ['permission:can delete']], function () {
                Route::delete('/speciment/{id}', [OpsSpecimentController::class, 'destroy'])->name('speciment.delete');
                Route::delete('/pajak-reklame/{id}', [OpsPajakReklameController::class, 'destroy'])->name('pajak-reklame.delete');
                Route::delete('/apar/detail/{id}', [OpsAparController::class, 'destroy_detail'])->name('apar.detail.delete');
                Route::delete('/skbirtgs/{id}', [OpsSkbirtgsController::class, 'destroy'])->name('skbirtgs.delete');
                Route::delete('/sk-operasional/{id}', [OpsSkOperasionalController::class, 'destroy'])->name('sk-operasional.delete');
                Route::delete('/apar/{id}', [OpsAparController::class, 'destroy'])->name('apar.delete');
            });
        });
    });
});

require __DIR__ . '/auth.php';
