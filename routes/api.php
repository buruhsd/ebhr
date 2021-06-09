<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// use App\Http\Controllers\HrController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Hr\EmployeeController;
use App\Http\Controllers\Admin\RoleController;
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

// Route::post('employee', 'HrController@getHRdata');
// Route::get('identity/list/json', [EmployeeController::class, 'getIdentityCard'])->name('api.employee.getIdentityCard');
// Route::get('getpaginate', [EmployeeController::class, 'getpaginate'])->name('api.employee.getpaginate');
// Route::get('employees', [EmployeeController::class, 'apiEmployee'])->name('api.employee.data');

Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
Route::get('/user1', [AuthController::class, 'user'])->name('api.user');

// //User Management
Route::get('/admin/user', [UserController::class, 'list'])->name('api.user.list');
Route::get('/admin/user/{user}', [UserController::class, 'show'])->name('api.user.show');
Route::post('/admin/user', [UserController::class, 'store'])->name('api.user.store');
Route::patch('/admin/user/{user}', [UserController::class, 'update'])->name('api.user.update');
Route::delete('/admin/user/{user}', [UserController::class, 'delete'])->name('api.user.delete');

//authorization management
Route::get('/admin/roles', [RoleController::class, 'index']);
Route::post('/admin/roles', [RoleController::class, 'store']);
Route::get('/admin/roles/{role}', [RoleController::class, 'show']);
Route::patch('/admin/roles/{role}', [RoleController::class, 'update']);
Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy']);

//HR Identity
Route::get('identity/list', [EmployeeController::class, 'IdentityCardList'])->name('api.employee.IdentityCardList');
Route::get('identity/show/{identityCard}', [EmployeeController::class, 'showIdentityCard'])->name('api.employee.showIdentityCard');
Route::post('identity/add', [EmployeeController::class, 'IdentityCardStore'])->name('api.employee.IdentityCardStore');
Route::patch('identity/update/{identityCard}', [EmployeeController::class, 'IdentityCardUpdate'])->name('api.employee.IdentityCardUpdate');

//Employee
Route::get('employee/list', [EmployeeController::class, 'EmployeeList'])->name('api.employee.EmployeeList');
Route::post('employee/add', [EmployeeController::class, 'createEmployee'])->name('api.employee.createEmployee');
Route::get('employee/show/{employee}', [EmployeeController::class, 'showEmployee'])->name('api.employee.showEmployee');


