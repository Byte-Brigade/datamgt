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
    Route::get('kdo_mobil/{id}', [GapApiController::class, 'kdo_mobil_details']);
    Route::get('kdo_mobils', [GapApiController::class, 'kdo_mobils']);
    Route::get('assets', [GapApiController::class, 'assets']);
    Route::get('perdins', [GapApiController::class, 'perdins']);
    Route::get('alihdayas', [GapApiController::class, 'alihdayas']);
    Route::get('alihdaya/{type}', [GapApiController::class, 'alihdaya_details']);
    Route::get('perdin/{divisi_pembebanan}', [GapApiController::class, 'perdin_details']);
    Route::get('scoring_projects', [GapApiController::class, 'scoring_projects']);
    Route::get('scoring_assessments', [GapApiController::class, 'scoring_assessments']);

});

Route::prefix('inquery')->name('inquery.')->group(function() {
    Route::get('assets', [InqueryApiController::class, 'assets']);
    Route::get('branches', [InqueryApiController::class, 'branches'])->name('branches');
    Route::get('licenses', [InqueryApiController::class, 'licenses'])->name('branches');

});
Route::prefix('infra')->name('infra.')->group(function () {

    Route::get('sewa-gedungs', [InfraApiController::class, 'sewa_gedungs']);
    Route::get('disnakers', [InfraApiController::class, 'disnakers']);
    Route::get('scoring_projects', [InfraApiController::class, 'scoring_projects']);
    Route::get('scoring_assessments', [InfraApiController::class, 'scoring_assessments']);

});
