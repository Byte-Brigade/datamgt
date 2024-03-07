<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GapAlihDayaController;
use App\Http\Controllers\GapAssetController;
use App\Http\Controllers\GapDisnakerController;
use App\Http\Controllers\GapKdoController;
use App\Http\Controllers\GapPerdinController;
use App\Http\Controllers\GapPksController;
use App\Http\Controllers\GapScoringAssessmentController;
use App\Http\Controllers\GapScoringController;
use App\Http\Controllers\GapScoringProjectController;
use App\Http\Controllers\GapStoController;
use App\Http\Controllers\GapTonerController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InfraBroController;
use App\Http\Controllers\InfraMaintenanceCostController;
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

Route::get('/home', function () {
    return Inertia::render('Cabang');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/files', [FileController::class, 'index'])->name('files');
    Route::get('/files/{table_name}', [FileController::class, 'detail'])->name('files.detail');
    Route::get('/files/download/{id}', [FileController::class, 'download'])->name('files.download');
    Route::delete('/files/delete/{id}', [FileController::class, 'delete'])->name('files.delete');

    Route::prefix('/inquery')->group(function () {
        Route::redirect('/', '/inquery/branch');
        Route::get('/branch', [InqueryController::class, 'branch'])->name('inquery.branch');
        Route::get('/assets', [InqueryController::class, 'assets'])->name('inquery.assets');
        Route::get('/staff', [InqueryController::class, 'staff'])->name('inquery.staff');

        Route::middleware(['check.slug'])->group(function () {
            Route::get('/branch/{slug}', [InqueryController::class, 'branchDetail'])->name('inquery.branch.detail');
            Route::get('/assets/{slug}', [InqueryController::class, 'asset_detail'])->name('inquery.assets.detail');
            Route::get('/assets/sto/{slug}', [InqueryController::class, 'sto_detail'])->name('inquery.assets.sto');
            Route::get('/staff/detail/{slug}', [InqueryController::class, 'staff_detail'])->name('inquery.staff.detail');
        });
        Route::get('/assets', [InqueryController::class, 'assets'])->name('inquery.assets');
        Route::post('/assets/sto/remark', [InqueryController::class, 'assets_remark'])->name('inquery.assets.remark');
        Route::get('/scorings', [InqueryController::class, 'scorings'])->name('inquery.scorings');
        Route::get('/licenses', [InqueryController::class, 'licenses'])->name('inquery.licenses');

        // Non Cabang
        Route::get('/bros', [ReportController::class, 'bros'])->name('inquery.bros');
        Route::get('/bros/export', [ReportController::class, 'bro_export'])->name('bros.export');
        Route::get('/bros/{category}', [ReportController::class, 'bro_category'])->name('bros.category');

        Route::get('/alihdayas', [InqueryController::class, 'alihdaya_summary'])->name('inquery.alihdayas.summary');
        Route::get('/alihdayas/{slug}', [InqueryController::class, 'alihdayas'])->name('inquery.alihdayas.branch');
        Route::get('/alihdayas/{slug}/detail', [InqueryController::class, 'alihdaya_detail'])->name('inquery.alihdayas.detail');
        Route::get('/alihdayas/detail/{type}', [GapAlihDayaController::class, 'detail'])->name('alihdayas.type');

        Route::get('/vendor', [ReportController::class, 'vendor'])->name('inquery.vendor');
    });

    Route::get('/kdos/mobil/{slug}', [GapKdoController::class, 'kdo_mobil'])->name('gap.kdos.mobil');

    Route::prefix('/reporting')->name('reporting.')->group(function () {
        Route::get('/branches', [ReportController::class, 'branches'])->name('branches');
        Route::get('/assets', [ReportController::class, 'assets'])->name('assets');
        Route::get('/assets/{type_name}', [ReportController::class, 'asset_detail'])->name('assets.detail');
        Route::get('/assets/detail/{slug}', [ReportController::class, 'asset_detail_branch'])->name('assets.detail.branch');
        Route::get('/licenses', [ReportController::class, 'licenses'])->name('licenses');
        Route::get('/branches/export', [ReportController::class, 'export_branches'])->name('branches.export');
        Route::get('/disnaker/{slug}', [ReportController::class, 'disnaker'])->name('disnaker');
    });

    Route::middleware('permission:can sto')->group(function () {
        Route::prefix('gap')->name('gap.')->group(function () {
            Route::post('/stos', [GapStoController::class, 'store'])->name('stos.store');
            Route::post('/stos/hasil_sto/{slug}', [GapStoController::class, 'store_hasil_sto'])->name('stos.store.hasil_sto');
        });
    });

    Route::middleware('role:superadmin|branch_ops|ga|procurement|admin')->group(function () {

        Route::prefix('gap')->name('gap.')->group(function () {
            /* [START] GA Procurement KDO */
            Route::get('/kdos', [GapKdoController::class, 'index'])->name('kdos');
            Route::get('/kdos/vendor/{vendor}', [GapKdoController::class, 'vendor'])->name('kdos.vendor');
            Route::get('/kdos/template', [GapKdoController::class, 'template'])->name('kdos.template');
            Route::get('/kdos/mobil/template', [GapKdoController::class, 'kdo_mobil_template'])->name('kdos.mobil.template');
            Route::post('/kdos/import', [GapKdoController::class, 'import'])->name('kdos.import');
            Route::post('/kdos/mobil/import', [GapKdoController::class, 'kdo_mobil_import'])->name('kdos.mobil.import');
            Route::post('/kdos', [GapKdoController::class, 'store'])->name('kdos.store');
            Route::post('/kdos/mobil/{id}', [GapKdoController::class, 'kdo_mobil_store'])->name('kdos.mobil.store');
            Route::put('/kdos/mobil/{id}', [GapKdoController::class, 'kdo_mobil_update'])->name('kdos.mobil.update');
            Route::delete('/kdos/mobil/{id}', [GapKdoController::class, 'kdo_mobil_destroy'])->name('kdos.mobil.destroy');
            Route::get('/kdos/export', [GapKdoController::class, 'export'])->name('kdos.export');
            Route::get('/kdos/mobil/{branch_code}/export', [GapKdoController::class, 'kdo_mobil_export'])->name('kdos.mobil.export');
            /* [END] GA Procurement KDO */
            Route::get('/maintenance', function () {
                abort(404);
            })->name('maintenance');

            Route::get('/assets', [GapAssetController::class, 'index'])->name('assets');
            Route::get('/assets/template', [GapAssetController::class, 'template'])->name('assets.template');
            Route::post('/assets/import', [GapAssetController::class, 'import'])->name('assets.import');
            Route::post('/assets', [GapAssetController::class, 'store'])->name('assets.store');
            Route::put('/assets/{id}', [GapAssetController::class, 'update'])->name('assets.update');
            Route::get('/assets/export', [GapAssetController::class, 'export'])->name('assets.export');
            Route::delete('/assets/{id}', [GapAssetController::class, 'destroy'])->name('assets.delete');

            Route::get('/scoring-projects', [GapScoringProjectController::class, 'index'])->name('scoring-projects');
            Route::get('/scoring-projects/template', [GapScoringProjectController::class, 'template'])->name('scoring-projects.template');
            Route::get('/scoring-projects/detail/{scoring_vendor}', [GapScoringProjectController::class, 'detail'])->name('scoring-projects.detail');
            Route::post('/scoring-projects/import', [GapScoringProjectController::class, 'import'])->name('scoring-projects.import');
            Route::post('/scoring-projects', [GapScoringProjectController::class, 'store'])->name('scoring-projects.store');
            Route::put('/scoring-projects/{id}', [GapScoringProjectController::class, 'update'])->name('scoring-projects.update');
            Route::get('/scoring-projects/export', [GapScoringProjectController::class, 'export'])->name('scoring-projects.export');
            Route::delete('/scoring-projects/{id}', [GapScoringProjectController::class, 'destroy'])->name('scoring-projects.delete');

            Route::get('/scoring-assessments', [GapScoringAssessmentController::class, 'index'])->name('scoring-assessments');
            Route::get('/scoring-assessments/template', [GapScoringAssessmentController::class, 'template'])->name('scoring-assessments.template');
            Route::get('/scoring-assessments/detail/{scoring_vendor}', [GapScoringAssessmentController::class, 'detail'])->name('scoring-assessments.detail');
            Route::post('/scoring-assessments/import', [GapScoringAssessmentController::class, 'import'])->name('scoring-assessments.import');
            Route::post('/scoring-assessments', [GapScoringAssessmentController::class, 'store'])->name('scoring-assessments.store');
            Route::put('/scoring-assessments/{id}', [GapScoringAssessmentController::class, 'update'])->name('scoring-assessments.update');
            Route::get('/scoring-assessments/export', [GapScoringAssessmentController::class, 'export'])->name('scoring-assessments.export');
            Route::delete('/scoring-assessments/{id}', [GapScoringAssessmentController::class, 'destroy'])->name('scoring-assessments.delete');

            Route::get('/perdins', [GapPerdinController::class, 'index'])->name('perdins');
            Route::get('/perdins/template', [GapPerdinController::class, 'template'])->name('perdins.template');
            Route::get('/perdins/detail/{divisi_pembebanan}', [GapPerdinController::class, 'detail'])->name('perdins.detail');
            Route::post('/perdins/import', [GapPerdinController::class, 'import'])->name('perdins.import');
            Route::post('/perdins', [GapPerdinController::class, 'store'])->name('perdins.store');
            Route::put('/perdins/{id}', [GapPerdinController::class, 'update'])->name('perdins.update');
            Route::get('/perdins/export', [GapPerdinController::class, 'export'])->name('perdins.export');
            Route::delete('/perdins/{id}', [GapPerdinController::class, 'destroy'])->name('perdins.delete');

            Route::get('/alihdayas', [GapAlihDayaController::class, 'index'])->name('alihdayas');
            Route::get('/alihdayas/template', [GapAlihDayaController::class, 'template'])->name('alihdayas.template');
            Route::get('/alihdayas/detail/{type}', [GapAlihDayaController::class, 'detail'])->name('alihdayas.type');
            Route::post('/alihdayas/import', [GapAlihDayaController::class, 'import'])->name('alihdayas.import');
            Route::post('/alihdayas', [GapAlihDayaController::class, 'store'])->name('alihdayas.store');
            Route::put('/alihdayas/{id}', [GapAlihDayaController::class, 'update'])->name('alihdayas.update');
            Route::get('/alihdayas/export', [GapAlihDayaController::class, 'export'])->name('alihdayas.export');
            Route::delete('/alihdayas/{id}', [GapAlihDayaController::class, 'destroy'])->name('alihdayas.delete');


            Route::get('/toners', [GapTonerController::class, 'index'])->name('toners');
            Route::get('/toners/template', [GapTonerController::class, 'template'])->name('toners.template');
            Route::get('/toners/detail/{slug}', [GapTonerController::class, 'detail'])->name('toners.detail');
            Route::post('/toners/import', [GapTonerController::class, 'import'])->name('toners.import');
            Route::post('/toners', [GapTonerController::class, 'store'])->name('toners.store');
            Route::put('/toners/{id}', [GapTonerController::class, 'update'])->name('toners.update');
            Route::get('/toners/export', [GapTonerController::class, 'export'])->name('toners.export');
            Route::delete('/toners/{id}', [GapTonerController::class, 'destroy'])->name('toners.delete');

            Route::get('/pks', [GapPksController::class, 'index'])->name('pks');
            Route::get('/pks/template', [GapPksController::class, 'template'])->name('pks.template');
            Route::get('/pks/detail/{status}', [GapPksController::class, 'detail'])->name('pks.detail');
            Route::post('/pks/import', [GapPksController::class, 'import'])->name('pks.import');
            Route::post('/pks', [GapPksController::class, 'store'])->name('pks.store');
            Route::put('/pks/{id}', [GapPksController::class, 'update'])->name('pks.update');
            Route::get('/pks/export', [GapPksController::class, 'export'])->name('pks.export');
            Route::delete('/pks/{id}', [GapPksController::class, 'destroy'])->name('pks.delete');

            Route::get('/stos', [GapStoController::class, 'index'])->name('stos');
            Route::get('/stos/detail/{gap_sto_id}', [GapStoController::class, 'detail'])->name('stos.detail');
            Route::get('/stos/template', [GapStoController::class, 'template'])->name('stos.template');
            Route::get('/stos/disclaimer', [GapStoController::class, 'disclaimer'])->name('stos.disclaimer');
            Route::post('/stos/import', [GapStoController::class, 'import'])->name('stos.import');

            Route::put('/stos/{id}', [GapStoController::class, 'update'])->name('stos.update');
            Route::get('/stos/export', [GapStoController::class, 'export'])->name('stos.export');
            Route::delete('/stos/{id}', [GapStoController::class, 'destroy'])->name('stos.delete');
        });
        Route::prefix('infra')->name('infra.')->group(function () {
            /* [START] GA Procurement Disnaker */
            Route::get('/disnaker', [GapDisnakerController::class, 'index'])->name('disnaker');
            Route::get('/disnaker/template', [GapDisnakerController::class, 'template'])->name('disnaker.template');
            Route::post('/disnaker/import', [GapDisnakerController::class, 'import'])->name('disnaker.import');
            Route::post('/disnaker', [GapDisnakerController::class, 'store'])->name('disnaker.store');
            Route::post('/disnaker/{id}/upload', [GapDisnakerController::class, 'upload'])->name('disnaker.upload');
            Route::post('/disnaker/{id}', [GapDisnakerController::class, 'update'])->name('disnaker.update');
            Route::get('/disnaker/detail/{id}', [GapDisnakerController::class, 'detail'])->name('disnaker.detail');
            Route::get('/disnaker/export', [GapDisnakerController::class, 'export'])->name('disnaker.export');
            Route::delete('/disnaker/{id}', [GapDisnakerController::class, 'destroy'])->name('disnaker.delete');
            /* [END] GA Procurement Disnaker */


            Route::get('/scoring-projects', [InfraScoringProjectController::class, 'index'])->name('scoring-projects');
            Route::get('/scoring-projects/template', [InfraScoringProjectController::class, 'template'])->name('scoring-projects.template');
            Route::get('/scoring-projects/detail/{scoring_vendor}/detail', [InfraScoringProjectController::class, 'detail'])->name('scoring-projects.detail');
            Route::post('/scoring-projects/import', [InfraScoringProjectController::class, 'import'])->name('scoring-projects.import');
            Route::post('/scoring-projects', [InfraScoringProjectController::class, 'store'])->name('scoring-projects.store');
            Route::put('/scoring-projects/{id}', [InfraScoringProjectController::class, 'update'])->name('scoring-projects.update');
            Route::get('/scoring-projects/export', [InfraScoringProjectController::class, 'export'])->name('scoring-projects.export');
            Route::delete('/scoring-projects/{id}', [InfraScoringProjectController::class, 'destroy'])->name('scoring-projects.delete');

            Route::get('/scoring-assessments', [InfraScoringAssessmentController::class, 'index'])->name('scoring-assessments');
            Route::get('/scoring-assessments/template', [InfraScoringAssessmentController::class, 'template'])->name('scoring-assessments.template');
            Route::get('/scoring-assessments/detail/{scoring_vendor}', [InfraScoringAssessmentController::class, 'detail'])->name('scoring-assessments.detail');
            Route::post('/scoring-assessments/import', [InfraScoringAssessmentController::class, 'import'])->name('scoring-assessments.import');
            Route::post('/scoring-assessments', [InfraScoringAssessmentController::class, 'store'])->name('scoring-assessments.store');
            Route::put('/scoring-assessments/{id}', [InfraScoringAssessmentController::class, 'update'])->name('scoring-assessments.update');
            Route::get('/scoring-assessments/export', [InfraScoringAssessmentController::class, 'export'])->name('scoring-assessments.export');
            Route::delete('/scoring-assessments/{id}', [InfraScoringAssessmentController::class, 'destroy'])->name('scoring-assessments.delete');

            Route::get('/sewa-gedungs', [InfraSewaGedungController::class, 'index'])->name('sewa_gedungs');
            Route::get('/sewa-gedungs/template', [InfraSewaGedungController::class, 'template'])->name('sewa_gedungs.template');
            Route::post('/sewa-gedungs/import', [InfraSewaGedungController::class, 'import'])->name('sewa_gedungs.import');
            Route::post('/sewa-gedungs', [InfraSewaGedungController::class, 'store'])->name('sewa_gedungs.store');
            Route::put('/sewa-gedungs/{id}', [InfraSewaGedungController::class, 'update'])->name('sewa_gedungs.update');
            Route::get('/sewa-gedungs/export', [InfraSewaGedungController::class, 'export'])->name('sewa_gedungs.export');
            Route::delete('/sewa-gedungs/{id}', [InfraSewaGedungController::class, 'destroy'])->name('sewa_gedungs.delete');

            Route::get('/bros', [InfraBroController::class, 'index'])->name('bros');
            Route::get('/bros/template', [InfraBroController::class, 'template'])->name('bros.template');
            Route::post('/bros/import', [InfraBroController::class, 'import'])->name('bros.import');
            Route::post('/bros', [InfraBroController::class, 'store'])->name('bros.store');
            Route::put('/bros/{id}', [InfraBroController::class, 'update'])->name('bros.update');
            Route::get('/bros/export', [InfraBroController::class, 'export'])->name('bros.export');
            Route::delete('/bros/{id}', [InfraBroController::class, 'destroy'])->name('bros.delete');

            Route::get('/maintenance-costs', [InfraMaintenanceCostController::class, 'index'])->name('maintenance-costs');
            Route::get('/maintenance-costs/detail/{jenis_pekerjaan}', [InfraMaintenanceCostController::class, 'detail'])->name('maintenance-costs.detail');
            Route::get('/maintenance-costs/template', [InfraMaintenanceCostController::class, 'template'])->name('maintenance-costs.template');
            Route::post('/maintenance-costs/import', [InfraMaintenanceCostController::class, 'import'])->name('maintenance-costs.import');
            Route::post('/maintenance-costs', [InfraMaintenanceCostController::class, 'store'])->name('maintenance-costs.store');
            Route::put('/maintenance-costs/{id}', [InfraMaintenanceCostController::class, 'update'])->name('maintenance-costs.update');
            Route::get('/maintenance-costs/export', [InfraMaintenanceCostController::class, 'export'])->name('maintenance-costs.export');
            Route::delete('/maintenance-costs/{id}', [InfraMaintenanceCostController::class, 'destroy'])->name('maintenance-costs.delete');
        });


        Route::prefix('ops')->name('ops.')->group(function () {

            /* [START] Branches */
            Route::get('/branches', [BranchController::class, 'index'])->name('branches');
            Route::get('/branches/template', [BranchController::class, 'template'])->name('branches.template');
            Route::post('/branches/import', [BranchController::class, 'import'])->name('branches.import');
            Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
            Route::post('/branches/upload/{id}', [BranchController::class, 'upload'])->name('branches.upload');

            Route::post('/branches/{id}', [BranchController::class, 'update'])->name('branches.update');
            Route::delete('/branches/{id}', [BranchController::class, 'destroy'])->name('branches.delete');
            Route::get('/branches/export', [BranchController::class, 'export'])->name('branches.export');
            /* [END] Branches */

            /* [START] Employees */
            Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
            Route::get('/employees/template', [EmployeeController::class, 'template'])->name('employees.template');

            Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
            Route::post('/employees/sync', [EmployeeController::class, 'sync'])->name('employees.sync');
            Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.delete');
            Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
            Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
            /* [END] Employees */

            /* [START] Ops SKBIRTGS */
            Route::get('/skbirtgs', [OpsSkbirtgsController::class, 'index'])->name('skbirtgs');
            Route::get('/skbirtgs/template', [OpsSkbirtgsController::class, 'template'])->name('skbirtgs.template');
            Route::post('/skbirtgs', [OpsSkbirtgsController::class, 'import'])->name('skbirtgs.import');
            Route::post('/skbirtgs/upload/{id}', [OpsSkbirtgsController::class, 'upload'])->name('skbirtgs.upload');
            Route::get('/skbirtgs/export', [OpsSkbirtgsController::class, 'export'])->name('skbirtgs.export');
            /* [END] Ops SKBIRTGS */

            /* [START] Ops SK Operasional Cabang */
            Route::get('/sk-operasional', [OpsSkOperasionalController::class, 'index'])->name('sk-operasional');
            Route::get('/sk-operasional/template', [OpsSkOperasionalController::class, 'template'])->name('sk-operasional.template');
            Route::post('/sk-operasional', [OpsSkOperasionalController::class, 'import'])->name('sk-operasional.import');
            Route::post('/sk-operasional/upload/{id}', [OpsSkOperasionalController::class, 'upload'])->name('sk-operasional.upload');
            Route::get('/sk-operasional/export', [OpsSkOperasionalController::class, 'export'])->name('sk-operasional.export');
            /* [END] Ops SK Operasional Cabang */

            /* [START] Ops Pajak Reklame */
            Route::get('/pajak-reklame', [OpsPajakReklameController::class, 'index'])->name('pajak-reklame');
            Route::get('/pajak-reklame/template', [OpsPajakReklameController::class, 'template'])->name('pajak-reklame.template');
            Route::post('/pajak-reklame/import', [OpsPajakReklameController::class, 'import'])->name('pajak-reklame.import');
            Route::post('/pajak-reklame', [OpsPajakReklameController::class, 'store'])->name('pajak-reklame.store');

            Route::post('/pajak-reklame/upload/{id}', [OpsPajakReklameController::class, 'upload'])->name('pajak-reklame.upload');
            Route::get('/pajak-reklame/export', [OpsPajakReklameController::class, 'export'])->name('pajak-reklame.export');
            /* [END] Ops Pajak Reklame */


            /* [START] Ops Speciment */
            Route::get('/speciment', [OpsSpecimentController::class, 'index'])->name('speciment');
            Route::get('/speciment/template', [OpsSpecimentController::class, 'template'])->name('speciment.template');
            Route::post('/speciment/import', [OpsSpecimentController::class, 'import'])->name('speciment.import');
            Route::post('/speciment', [OpsSpecimentController::class, 'store'])->name('speciment.store');
            Route::post('/speciment/upload/{id}', [OpsSpecimentController::class, 'upload'])->name('speciment.upload');
            Route::get('/speciment/export', [OpsSpecimentController::class, 'export'])->name('speciment.export');
            /* [END] Ops Speciment */


            /* [START] Ops APAR */
            Route::get('/apar', [OpsAparController::class, 'index'])->name('apar');
            Route::get('/apar/template', [OpsAparController::class, 'template'])->name('apar.template');
            Route::post('/apar/import', [OpsAparController::class, 'import'])->name('apar.import');
            Route::post('/apar', [OpsAparController::class, 'store'])->name('apar.store');


            Route::get('/apar/detail/{slug}', [OpsAparController::class, 'detail'])->name('apar.detail');
            Route::get('/apar/export', [OpsAparController::class, 'export'])->name('apar.export');
            /* [END] Ops APAR */

            Route::group(['middleware' => ['role:branch_ops|superadmin']], function () {
                Route::group(['middleware' => ['permission:can edit']], function () {
                    Route::post('/skbirtgs/{id}', [OpsSkbirtgsController::class, 'update'])->name('skbirtgs.update');
                    Route::post('/sk-operasional/{id}', [OpsSkOperasionalController::class, 'update'])->name('sk-operasional.update');
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

    Route::middleware('role:superadmin|admin')->group(function () {
        /* [START] User Access Management */
        Route::get('/uam', [UAMController::class, 'index'])->name('uam');
        Route::post('/uam', [UAMController::class, 'store'])->name('uam.store');
        Route::put('/uam/{id}', [UAMController::class, 'update'])->name('uam.update');
        Route::delete('/uam/{id}', [UAMController::class, 'destroy'])->name('uam.delete');
        /* [END] User Access Management*/
    });
});

require __DIR__ . '/auth.php';
