<?php

use App\Http\Controllers\API\GapApiController;
use App\Http\Controllers\API\InfraApiController;
use App\Http\Controllers\API\InqueryApiController;
use App\Http\Controllers\API\OpsApiController;
use App\Http\Controllers\API\ReportApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UAMController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('report')->name('report.')->group(function() {
    Route::get('branches', [ReportApiController::class, 'branches'])->name('branches');
    Route::get('bros', [ReportApiController::class, 'bros'])->name('bros');
    Route::get('disnaker/{id}', [ReportApiController::class, 'disnaker_details']);

});
Route::prefix('ops')->name('ops.')->group(function() {
    Route::get('/branches', [OpsApiController::class, 'branches']);
    Route::get('/employees', [OpsApiController::class, 'employees']);
    Route::get('/apars', [OpsApiController::class, 'apars']);
    Route::get('/apar/details/{id}', [OpsApiController::class, 'apar_details']);
    Route::get('/pajak-reklames', [OpsApiController::class, 'pajak_reklames']);
    Route::get('/skbirtgs', [OpsApiController::class, 'skbirtgs']);
    Route::get('/sk-operasionals', [OpsApiController::class, 'sk_operasionals']);
    Route::get('/speciments', [OpsApiController::class, 'speciments']);
});

Route::get('uam', [UAMController::class, 'api']);
Route::get('/dashboard/branch', [DashboardController::class, 'api']);

Route::prefix('gap')->name('gap.')->group(function () {
   Route::get('kdos/{type}', [GapApiController::class, 'kdos']);
    Route::get('kdos/{branch_id}/detail', [GapApiController::class, 'kdo_mobil_details']);
    Route::get('assets', [GapApiController::class, 'assets']);
    Route::get('perdins', [GapApiController::class, 'perdins']);
    Route::get('toners', [GapApiController::class, 'toners']);
    Route::get('stos', [GapApiController::class, 'stos']);
    Route::get('pks', [GapApiController::class, 'pks']);
    Route::get('pks/{status}', [GapApiController::class, 'pks_details']);
    Route::get('toners/{branch_id}', [GapApiController::class, 'toner_details']);
    Route::get('alihdayas', [GapApiController::class, 'alihdayas']);
    Route::get('alihdaya/{type}', [GapApiController::class, 'alihdaya_details']);
    Route::get('perdin/{divisi_pembebanan}', [GapApiController::class, 'perdin_details']);
    Route::get('scoring_projects', [GapApiController::class, 'scoring_projects']);
    Route::get('scoring_projects/{scoring_vendor}', [GapApiController::class, 'scoring_project_details']);
    Route::get('scoring_assessments', [GapApiController::class, 'scoring_assessments']);
    Route::get('scoring_assessments/{scoring_vendor}', [GapApiController::class, 'scoring_assessment_details']);

});

Route::prefix('inquery')->name('inquery.')->group(function() {
    Route::get('assets', [InqueryApiController::class, 'assets']);
    Route::get('branches', [InqueryApiController::class, 'branches'])->name('branches');
    Route::get('licenses', [InqueryApiController::class, 'licenses'])->name('licenses');
    Route::get('kdos', [InqueryApiController::class, 'kdos']);
    Route::get('stos', [InqueryApiController::class, 'stos'])->name('stos');
});
Route::prefix('infra')->name('infra.')->group(function () {

    Route::get('sewa-gedungs', [InfraApiController::class, 'sewa_gedungs']);
    Route::get('bros', [InfraApiController::class, 'bros']);
    Route::get('maintenance-costs', [InfraApiController::class, 'maintenance_costs']);
    Route::get('maintenance-costs/{jenis_pekerjaan}', [InfraApiController::class, 'maintenance_cost_details']);
    Route::get('disnakers', [InfraApiController::class, 'disnakers']);
    Route::get('scoring_projects', [InfraApiController::class, 'scoring_projects']);
    Route::get('scoring_projects/{scoring_vendor}', [InfraApiController::class, 'scoring_project_details']);
    Route::get('scoring_assessments', [InfraApiController::class, 'scoring_assessments']);
    Route::get('scoring_assessments/{scoring_vendor}', [InfraApiController::class, 'scoring_assessment_details']);

});
