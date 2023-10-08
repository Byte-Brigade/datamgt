<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OpsAparController;
use App\Http\Controllers\OpsPajakReklameController;
use App\Http\Controllers\OpsSkbirtgsController;
use App\Http\Controllers\OpsSkOperasionalController;
use App\Http\Controllers\OpsSpecimentController;
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

Route::prefix('component')->name('component.')->group(function () {
    Route::get('branches', [ComponentController::class, 'branches']);
    Route::get('branch_types', [ComponentController::class, 'branch_types']);
});
