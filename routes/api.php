<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// use App\Http\Controllers\HrController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Hr\EmployeeController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('employee', 'HrController@getHRdata');
Route::get('identity/list/json', [EmployeeController::class, 'getIdentityCard'])->name('api.employee.getIdentityCard');
Route::get('getpaginate', [EmployeeController::class, 'getpaginate'])->name('api.employee.getpaginate');

Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
Route::get('/user1', [AuthController::class, 'user'])->name('api.user');

//User Management
Route::get('/admin/user', [UserController::class, 'list'])->name('api.user.list');

