<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GapAlihDayaController;
use App\Http\Controllers\GapAssetController;
use App\Http\Controllers\GapDisnakerController;
use App\Http\Controllers\GapKdoController;
use App\Http\Controllers\GapPerdinController;
use App\Http\Controllers\GapScoringAssessmentController;
use App\Http\Controllers\GapScoringController;
use App\Http\Controllers\GapScoringProjectController;
use App\Http\Controllers\InfraScoringAssessmentController;
use App\Http\Controllers\InfraScoringProjectController;
use App\Http\Controllers\InfraSewaGedungController;
use App\Http\Controllers\InqueryController;
use App\Http\Controllers\OpsAparController;
use App\Http\Controllers\OpsPajakReklameController;
use App\Http\Controllers\OpsSkbirtgsController;
use App\Http\Controllers\OpsSkOperasionalController;
use App\Http\Controllers\OpsSpecimentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
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


Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::get('/maintenance', function () {
    abort(404);
})->name('maintenance');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('/inquery')->group(function () {
        Route::redirect('/', '/inquery/branch');
        Route::get('/branch', [InqueryController::class, 'branch'])->name('inquery.branch');
        Route::get('/branch/{id}', [InqueryController::class, 'branchDetail'])->name('inquery.branch.detail');
        Route::get('/staff', [InqueryController::class, 'branch'])->name('inquery.staff');
        Route::get('/assets', [InqueryController::class, 'assets'])->name('inquery.assets');
        Route::get('/assets/{id}', [InqueryController::class, 'asset_detail'])->name('inquery.assets.detail');
        Route::get('/scorings', [InqueryController::class, 'scorings'])->name('inquery.scorings');
        Route::get('/licenses', [InqueryController::class, 'licenses'])->name('inquery.licenses');
    });

    Route::prefix('/reporting')->name('reporting.')->group(function () {
        Route::get('/branches', [ReportController::class, 'branches'])->name('branches');
        Route::get('/branches/export', [ReportController::class, 'export_branches'])->name('branches.export');
        Route::get('/disnaker/{branch_code}', [ReportController::class, 'disnaker'])->name('disnaker');
    });

    Route::middleware('role:superadmin|branch_ops|ga|procurement')->group(function () {

        Route::prefix('gap')->name('gap.')->group(function () {
            /* [START] GA Procurement KDO */
            Route::get('/kdo', [GapKdoController::class, 'index'])->name('kdo');
            Route::post('/kdo/import', [GapKdoController::class, 'import'])->name('kdo.import');
            Route::post('/kdo/mobil/import', [GapKdoController::class, 'kdo_mobil_import'])->name('kdo.mobil.import');
            Route::post('/kdo', [GapKdoController::class, 'store'])->name('kdo.store');
            Route::get('/kdo/mobil/{branch_code}', [GapKdoController::class, 'kdo_mobil'])->name('kdo.mobil');
            Route::post('/kdo/mobil/{id}', [GapKdoController::class, 'kdo_mobil_store'])->name('kdo.mobil.store');
            Route::put('/kdo/mobil/{id}', [GapKdoController::class, 'kdo_mobil_update'])->name('kdo.mobil.update');
            Route::delete('/kdo/mobil/{branch_code}/{id}', [GapKdoController::class, 'kdo_mobil_destroy'])->name('kdo.mobil.destroy');
            Route::get('/kdo/export', [GapKdoController::class, 'export'])->name('kdo.export');
            Route::get('/kdo/mobil/{branch_code}/export', [GapKdoController::class, 'kdo_mobil_export'])->name('kdo.mobil.export');
            /* [END] GA Procurement KDO */
            Route::get('/maintenance', function () {
                abort(404);
            })->name('maintenance');

            Route::get('/assets', [GapAssetController::class, 'index'])->name('assets');
            Route::post('/assets/import', [GapAssetController::class, 'import'])->name('assets.import');
            Route::post('/assets', [GapAssetController::class, 'store'])->name('assets.store');
            Route::put('/assets/{id}', [GapAssetController::class, 'update'])->name('assets.update');
            Route::get('/assets/export', [GapAssetController::class, 'export'])->name('assets.export');
            Route::delete('/assets/{id}', [GapAssetController::class, 'destroy'])->name('assets.delete');

            Route::get('/scoring-projects', [GapScoringProjectController::class, 'index'])->name('scoring_projects');
            Route::get('/scoring-projects/{scoring_vendor}', [GapScoringProjectController::class, 'detail'])->name('scoring_projects.detail');
            Route::post('/scoring-projects/import', [GapScoringProjectController::class, 'import'])->name('scoring_projects.import');
            Route::post('/scoring-projects', [GapScoringProjectController::class, 'store'])->name('scoring_projects.store');
            Route::put('/scoring-projects/{id}', [GapScoringProjectController::class, 'update'])->name('scoring_projects.update');
            Route::get('/scoring-projects/export', [GapScoringProjectController::class, 'export'])->name('scoring_projects.export');
            Route::delete('/scoring-projects/{id}', [GapScoringProjectController::class, 'destroy'])->name('scoring_projects.delete');

            Route::get('/scoring-assessments', [GapScoringAssessmentController::class, 'index'])->name('scoring_assessments');
            Route::get('/scoring-assessments/{scoring_vendor}', [GapScoringAssessmentController::class, 'detail'])->name('scoring_assessments.detail');
            Route::post('/scoring-assessments/import', [GapScoringAssessmentController::class, 'import'])->name('scoring_assessments.import');
            Route::post('/scoring-assessments', [GapScoringAssessmentController::class, 'store'])->name('scoring_assessments.store');
            Route::put('/scoring-assessments/{id}', [GapScoringAssessmentController::class, 'update'])->name('scoring_assessments.update');
            Route::get('/scoring-assessments/export', [GapScoringAssessmentController::class, 'export'])->name('scoring_assessments.export');
            Route::delete('/scoring-assessments/{id}', [GapScoringAssessmentController::class, 'destroy'])->name('scoring_assessments.delete');

            Route::get('/perdins', [GapPerdinController::class, 'index'])->name('perdins');
            Route::get('/perdins/{divisi_pembebanan}', [GapPerdinController::class, 'detail'])->name('perdins.detail');
            Route::post('/perdins/import', [GapPerdinController::class, 'import'])->name('perdins.import');
            Route::post('/perdins', [GapPerdinController::class, 'store'])->name('perdins.store');
            Route::put('/perdins/{id}', [GapPerdinController::class, 'update'])->name('perdins.update');
            Route::get('/perdins/export', [GapPerdinController::class, 'export'])->name('perdins.export');
            Route::delete('/perdins/{id}', [GapPerdinController::class, 'destroy'])->name('perdins.delete');

            Route::get('/alihdayas', [GapAlihDayaController::class, 'index'])->name('alihdayas');
            Route::get('/alihdayas/{type}', [GapAlihDayaController::class, 'detail'])->name('alihdayas.type');
            Route::post('/alihdayas/import', [GapAlihDayaController::class, 'import'])->name('alihdayas.import');
            Route::post('/alihdayas', [GapAlihDayaController::class, 'store'])->name('alihdayas.store');
            Route::put('/alihdayas/{id}', [GapAlihDayaController::class, 'update'])->name('alihdayas.update');
            Route::get('/alihdayas/export', [GapAlihDayaController::class, 'export'])->name('alihdayas.export');
            Route::delete('/alihdayas/{id}', [GapAlihDayaController::class, 'destroy'])->name('alihdayas.delete');
        });
        Route::prefix('infra')->name('infra.')->group(function () {
            /* [START] GA Procurement Disnaker */
            Route::get('/disnaker', [GapDisnakerController::class, 'index'])->name('disnaker');
            Route::post('/disnaker/import', [GapDisnakerController::class, 'import'])->name('disnaker.import');
            Route::post('/disnaker', [GapDisnakerController::class, 'store'])->name('disnaker.store');
            Route::post('/disnaker/{id}/upload', [GapDisnakerController::class, 'upload'])->name('disnaker.upload');
            Route::post('/disnaker/{id}', [GapDisnakerController::class, 'update'])->name('disnaker.update');
            Route::get('/disnaker/detail/{id}', [GapDisnakerController::class, 'detail'])->name('disnaker.detail');
            Route::get('/disnaker/export', [GapDisnakerController::class, 'export'])->name('disnaker.export');
            Route::delete('/disnaker/{id}', [GapDisnakerController::class, 'destroy'])->name('disnaker.delete');
            /* [END] GA Procurement Disnaker */


            Route::get('/scoring-projects', [InfraScoringProjectController::class, 'index'])->name('scoring_projects');
            Route::get('/scoring-projects/{scoring_vendor}', [InfraScoringProjectController::class, 'detail'])->name('scoring_projects.detail');
            Route::post('/scoring-projects/import', [InfraScoringProjectController::class, 'import'])->name('scoring_projects.import');
            Route::post('/scoring-projects', [InfraScoringProjectController::class, 'store'])->name('scoring_projects.store');
            Route::put('/scoring-projects/{id}', [InfraScoringProjectController::class, 'update'])->name('scoring_projects.update');
            Route::get('/scoring-projects/export', [InfraScoringProjectController::class, 'export'])->name('scoring_projects.export');
            Route::delete('/scoring-projects/{id}', [InfraScoringProjectController::class, 'destroy'])->name('scoring_projects.delete');

            Route::get('/scoring-assessments', [InfraScoringAssessmentController::class, 'index'])->name('scoring_assessments');
            Route::get('/scoring-assessments/{scoring_vendor}', [InfraScoringAssessmentController::class, 'detail'])->name('scoring_assessments.detail');
            Route::post('/scoring-assessments/import', [InfraScoringAssessmentController::class, 'import'])->name('scoring_assessments.import');
            Route::post('/scoring-assessments', [InfraScoringAssessmentController::class, 'store'])->name('scoring_assessments.store');
            Route::put('/scoring-assessments/{id}', [InfraScoringAssessmentController::class, 'update'])->name('scoring_assessments.update');
            Route::get('/scoring-assessments/export', [InfraScoringAssessmentController::class, 'export'])->name('scoring_assessments.export');
            Route::delete('/scoring-assessments/{id}', [InfraScoringAssessmentController::class, 'destroy'])->name('scoring_assessments.delete');

            Route::get('/sewa-gedungs', [InfraSewaGedungController::class, 'index'])->name('sewa_gedungs');
            Route::post('/sewa-gedungs/import', [InfraSewaGedungController::class, 'import'])->name('sewa_gedungs.import');
            Route::post('/sewa-gedungs', [InfraSewaGedungController::class, 'store'])->name('sewa_gedungs.store');
            Route::put('/sewa-gedungs/{id}', [InfraSewaGedungController::class, 'update'])->name('sewa_gedungs.update');
            Route::get('/sewa-gedungs/export', [InfraSewaGedungController::class, 'export'])->name('sewa_gedungs.export');
            Route::delete('/sewa-gedungs/{id}', [InfraSewaGedungController::class, 'destroy'])->name('sewa_gedungs.delete');

            Route::get('/maintenance', function () {
                abort(404);
            })->name('maintenance');
        });


        Route::prefix('ops')->name('ops.')->group(function () {

            /* [START] Branches */
            Route::get('/branches', [BranchController::class, 'index'])->name('branches');
            Route::post('/branches/import', [BranchController::class, 'import'])->name('branches.import');
            Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
            Route::post('/branches/{id}', [BranchController::class, 'upload'])->name('branches.upload');

            Route::put('/branches/{id}', [BranchController::class, 'update'])->name('branches.update');
            Route::delete('/branches/{id}', [BranchController::class, 'destroy'])->name('branches.delete');
            Route::get('/branches/export', [BranchController::class, 'export'])->name('branches.export');
            /* [END] Branches */

            /* [START] Employees */
            Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
            Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
            Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.delete');
            Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
            Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
            /* [END] Employees */

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
            Route::post('/pajak-reklame/import', [OpsPajakReklameController::class, 'import'])->name('pajak-reklame.import');
            Route::post('/pajak-reklame', [OpsPajakReklameController::class, 'store'])->name('pajak-reklame.store');

            Route::post('/pajak-reklame/{id}', [OpsPajakReklameController::class, 'upload'])->name('pajak-reklame.upload');
            Route::get('/pajak-reklame/export', [OpsPajakReklameController::class, 'export'])->name('pajak-reklame.export');
            /* [END] Ops Pajak Reklame */


            /* [START] Ops Speciment */
            Route::get('/speciment', [OpsSpecimentController::class, 'index'])->name('speciment');
            Route::post('/speciment/import', [OpsSpecimentController::class, 'import'])->name('speciment.import');
            Route::post('/speciment', [OpsSpecimentController::class, 'store'])->name('speciment.store');
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

            Route::group(['middleware' => ['role:branch_ops|superadmin']], function () {
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

    Route::middleware('role:superadmin')->group(function () {
        /* [START] User Access Management */
        Route::get('/uam', [UAMController::class, 'index'])->name('uam');
        Route::post('/uam', [UAMController::class, 'store'])->name('uam.store');
        Route::put('/uam/{id}', [UAMController::class, 'update'])->name('uam.update');
        Route::delete('/uam/{id}', [UAMController::class, 'destroy'])->name('uam.delete');
        /* [END] User Access Management*/
    });
});

require __DIR__ . '/auth.php';
