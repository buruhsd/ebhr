<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// use App\Http\Controllers\HrController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Hr\EmployeeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\ProductController;

use App\Http\Controllers\Purchase\PurchaseController;
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

//Master
Route::get('/detail/nik', [MasterController::class, 'detailNik'])->name('api.detailNik');
Route::get('/provinces/list', [MasterController::class, 'provinceList'])->name('api.provinceList');
Route::get('/regencies/list/', [MasterController::class, 'regencyList'])->name('api.regencyList');
Route::get('/districts/list/', [MasterController::class, 'districtList'])->name('api.districtList');
Route::get('/villages/list/{id}', [MasterController::class, 'villageList'])->name('api.villageList');

Route::get('/postalcode/list', [MasterController::class, 'postalCodeList'])->name('api.postalCodeList');
Route::get('/worktype/list', [MasterController::class, 'workType'])->name('api.workType');
Route::get('/maritalstatus/list', [MasterController::class, 'maritalStatusList'])->name('api.maritalStatusList');
Route::get('/religions/list', [MasterController::class, 'religionList'])->name('api.religionList');
Route::get('/position', [MasterController::class, 'position'])->name('api.position');
Route::get('/workgroup', [MasterController::class, 'workGroup'])->name('api.workGroup');
Route::get('/workpattern', [MasterController::class, 'workPattern'])->name('api.workPattern');
Route::get('/employeeStatus', [MasterController::class, 'employeeStatus'])->name('api.employeeStatus');
Route::get('/developmentStatus', [MasterController::class, 'developmentStatus'])->name('api.developmentStatus');

// Master Purchase
Route::get('/branch', [MasterController::class, 'branch'])->name('api.branch');
Route::get('/product/list', [ProductController::class, 'index'])->name('api.product');
Route::get('/transactionType', [MasterController::class, 'transactionType'])->name('api.transactionType');
Route::get('/purchaseCategory', [MasterController::class, 'purchaseCategory'])->name('api.purchaseCategory');
Route::get('/purchaseUrgentity', [MasterController::class, 'purchaseUrgentity'])->name('api.purchaseUrgentity');
Route::get('/purchaseNecessary', [MasterController::class, 'purchaseNecessary'])->name('api.purchaseNecessary');

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
Route::get('/admin/permissions/list', [RoleController::class, 'permissionList']);

//HR Identity
Route::get('identity/search', [EmployeeController::class, 'searchIdentityCard'])->name('api.employee.searchIdentityCard');
Route::get('identity/list', [EmployeeController::class, 'IdentityCardList'])->name('api.employee.IdentityCardList');
Route::get('identity/show/{identityCard}', [EmployeeController::class, 'showIdentityCard'])->name('api.employee.showIdentityCard');
Route::post('identity/add', [EmployeeController::class, 'IdentityCardStore'])->name('api.employee.IdentityCardStore');
Route::patch('identity/update/{identityCard}', [EmployeeController::class, 'IdentityCardUpdate'])->name('api.employee.IdentityCardUpdate');

//Employee
Route::get('employee/list', [EmployeeController::class, 'EmployeeList'])->name('api.employee.EmployeeList');
Route::post('employee/add', [EmployeeController::class, 'createEmployee'])->name('api.employee.createEmployee');
Route::get('employee/show/{employee}', [EmployeeController::class, 'showEmployee'])->name('api.employee.showEmployee');
Route::patch('employee/update/{employee}', [EmployeeController::class, 'EmployeeUpdate'])->name('api.employee.EmployeeUpdate');

//Purchase
Route::get('purchase/list', [PurchaseController::class, 'index'])->name('api.purchase.index');
Route::get('purchase/{purchase}', [PurchaseController::class, 'show'])->name('api.purchase.show');
Route::post('purchase/create', [PurchaseController::class, 'createPurchaseLetter'])->name('api.purchase.createPurchaseLetter');
Route::patch('purchase/update/{purchase}', [PurchaseController::class, 'update'])->name('api.purchase.update');
Route::delete('purchase/{purchase}', [PurchaseController::class, 'delete'])->name('api.purchase.delete');
Route::get('purchase/{purchase}/approval', [PurchaseController::class, 'approval'])->name('api.purchase.approval');
//Order
Route::post('purchase/{purchase}/create-order', [PurchaseController::class, 'createOrder'])->name('api.purchase.createOrder');
Route::post('purchase/{order}/close-order', [PurchaseController::class, 'closeOrder'])->name('api.purchase.closeOrder');
Route::post('purchase/{purchase}/close-purchase', [PurchaseController::class, 'closePurchaseLetter'])->name('api.purchase.closePurchaseLetter');
