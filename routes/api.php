<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GapAssetController;
use App\Http\Controllers\GapDisnakerController;
use App\Http\Controllers\GapKdoController;
use App\Http\Controllers\OpsAparController;
use App\Http\Controllers\OpsPajakReklameController;
use App\Http\Controllers\OpsSkbirtgsController;
use App\Http\Controllers\OpsSkOperasionalController;
use App\Http\Controllers\OpsSpecimentController;
use App\Http\Controllers\ReportController;
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

Route::get('/branches', [BranchController::class, 'api']);
Route::get('/employees', [EmployeeController::class, 'api']);
Route::get('/ops/skbirtgs', [OpsSkbirtgsController::class, 'api']);
Route::get('/ops/sk-operasional', [OpsSkOperasionalController::class, 'api']);
Route::get('/ops/pajak-reklame', [OpsPajakReklameController::class, 'api']);
Route::get('/ops/speciment', [OpsSpecimentController::class, 'api']);
Route::get('/ops/apar', [OpsAparController::class, 'api']);
Route::get('/ops/apar/detail/{id}', [OpsAparController::class, 'api_detail']);
Route::get('uam', [UAMController::class, 'api']);
Route::get('/dashboard/branch', [DashboardController::class, 'api']);

Route::prefix('gap')->name('gap.')->group(function () {
    Route::get('kdo/mobil/{id}', [GapKdoController::class, 'api_kdo_mobil']);
    Route::get('kdo', [GapKdoController::class, 'api']);
    Route::get('assets', [GapAssetController::class, 'api']);

});
Route::prefix('infra')->name('infra.')->group(function () {

    Route::get('disnaker', [GapDisnakerController::class, 'api']);
    Route::get('disnaker/{id}/report', [ReportController::class, 'api_detail']);
});
