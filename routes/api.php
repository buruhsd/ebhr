<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// use App\Http\Controllers\HrController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Hr\EmployeeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Master\WorkStatusController;
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
Route::get('/searchRegency', [MasterController::class, 'searchRegency'])->name('api.searchRegency');

Route::get('/postalcode/list', [MasterController::class, 'postalCodeList'])->name('api.postalCodeList');
Route::get('/maritalstatus/list', [MasterController::class, 'maritalStatusList'])->name('api.maritalStatusList');
Route::get('/religions/list', [MasterController::class, 'religionList'])->name('api.religionList');
Route::get('/position', [MasterController::class, 'position'])->name('api.position');
Route::get('/worktype', [MasterController::class, 'workType'])->name('api.workType');
Route::get('/workgroup', [MasterController::class, 'workGroup'])->name('api.workGroup');
Route::get('/workpattern', [MasterController::class, 'workPattern'])->name('api.workPattern');
Route::get('/employeeStatus', [MasterController::class, 'employeeStatus'])->name('api.employeeStatus');
Route::get('/developmentStatus', [MasterController::class, 'developmentStatus'])->name('api.developmentStatus');
Route::get('/pointHire', [MasterController::class, 'pointHire'])->name('api.pointHire');
Route::get('/ranks', [MasterController::class, 'rank'])->name('api.rank');
Route::get('/organizations', [MasterController::class, 'organization'])->name('api.organization');
Route::get('/units', [MasterController::class, 'unit'])->name('api.units');
Route::get('/warehouse/{id}', [MasterController::class, 'warehouse'])->name('api.warehouse');
Route::get('/products', [MasterController::class, 'products'])->name('api.products');
Route::get('/getNumberProducts', [MasterController::class, 'getNumberProducts'])->name('api.getNumberProducts');
Route::get('/products/category', [MasterController::class, 'product_category'])->name('api.product_category');

// Master Purchase
Route::get('/branch', [MasterController::class, 'branch'])->name('api.branch');
Route::get('/product/list', [ProductController::class, 'index'])->name('api.productList');
Route::get('/transactionType', [MasterController::class, 'transactionType'])->name('api.transactionType');
Route::get('/purchaseCategory', [MasterController::class, 'purchaseCategory'])->name('api.purchaseCategory');
Route::get('/purchaseUrgentity', [MasterController::class, 'purchaseUrgentity'])->name('api.purchaseUrgentity');
Route::get('/purchaseNecessary', [MasterController::class, 'purchaseNecessary'])->name('api.purchaseNecessary');
Route::get('/work_status/list', [MasterController::class, 'workStatus'])->name('api.workStatus');

Route::group(['namespace' => 'Master','prefix'=>'master'], function() {
    Route::resource('branch', 'BranchController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('rank', 'RankController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('organization', 'OrganizationController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('development_status', 'DevelopmentStatusController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('employee_status', 'EmployeeStatusController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('marital_status', 'MaritalStatusController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('position', 'PositionController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('plafon', 'PlafonController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('supplier', 'SupplierController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('religion', 'ReligionController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('purchase_category', 'PurchaseCategoryController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('purchase_necessary', 'PurchaseNecessaryController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('purchase_urgentity', 'PurchaseUrgentityController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('transaction_type', 'TransactionTypeController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('unit', 'UnitController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('work_group', 'WorkGroupController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('work_pattern', 'WorkPatternController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('work_type', 'WorkTypeController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('work_status', 'WorkStatusController', ['only' => [
        'index','list','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('point_hire', 'PointHireController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('ware_house', 'WarehouseController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
    Route::get('/warehouse/getData', [App\Http\Controllers\Master\WarehouseController::class, 'getData']);

    Route::resource('product_category', 'ProducCategoryController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('product', 'ProductController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('product_status', 'ProductStatusController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('product_serial_number', 'ProductSerialNumberController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('bpb_type', 'BpbTypeController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
    Route::get('/bpb_types', [App\Http\Controllers\Master\BpbTypeController::class, 'getData']);

    Route::resource('pbp_type', 'PbpTypeController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
    Route::get('/pbp_types', [App\Http\Controllers\Master\PbpTypeController::class, 'getData']);

    Route::resource('supplier_status', 'SupplierStatusController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('usage_group', 'UsageGroupController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
    Route::get('/usage_groups', [App\Http\Controllers\Master\UsageGroupController::class, 'getData']);

    Route::resource('user_unit', 'UserUnitController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
    Route::get('/user_units', [App\Http\Controllers\Master\UserUnitController::class, 'getData']);

    Route::resource('supplier_category', 'SupplierCategoryController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('currency', 'CurrencyController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('kurs', 'KursController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('kurs_type', 'KursTypeController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('npwp', 'NpwpController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('partner', 'PartnerController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
});

Route::get('/suppliers', [App\Http\Controllers\Master\SupplierController::class, 'getData'])->name('api.supplier.data');
Route::get('/suppliers/status', [App\Http\Controllers\Master\SupplierController::class, 'getDataNotStatus'])->name('api.supplier.getDataNotStatus');
Route::get('/supplier_categories', [App\Http\Controllers\Master\SupplierCategoryController::class, 'getData'])->name('api.supplier_category.data');
Route::get('/supplier_category/parent', [App\Http\Controllers\Master\SupplierCategoryController::class, 'getParent'])->name('api.supplier_category.parent');
Route::get('/supplier_category/childs', [App\Http\Controllers\Master\SupplierCategoryController::class, 'getChilds'])->name('api.supplier_category.childs');
Route::get('/npwp/data', [App\Http\Controllers\Master\NpwpController::class, 'getData'])->name('api.npwp.data');
Route::get('/partner/code', [App\Http\Controllers\Master\PartnerController::class, 'getCode'])->name('api.partner.code');
Route::get('/partner/data', [App\Http\Controllers\Master\PartnerController::class, 'getData'])->name('api.partner.data');
Route::get('/currency/data', [App\Http\Controllers\Master\CurrencyController::class, 'getData'])->name('api.currency.data');
Route::get('/product_categories', [App\Http\Controllers\Master\ProducCategoryController::class, 'getData'])->name('api.product_category.data');
Route::get('/kurs_type/data', [App\Http\Controllers\Master\KursTypeController::class, 'getData'])->name('api.kurs_type.data');
Route::get('/kurs/currency', [App\Http\Controllers\Master\KursController::class, 'getKurs'])->name('api.kurs.getKurs');
Route::get('/product_status/data', [App\Http\Controllers\Master\ProductStatusController::class, 'getData'])->name('api.product_status.data');
Route::get('/product/approve/{id}', [App\Http\Controllers\Master\ProductController::class, 'approve'])->name('api.product.approve');

// Excel
Route::get('/organizations/excel', [App\Http\Controllers\Master\OrganizationController::class, 'export_excel'])->name('api.organization.export_excel');
Route::get('/positions/excel', [App\Http\Controllers\Master\PositionController::class, 'export_excel'])->name('api.position.export_excel');

// User Management
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
Route::get('employee/nomor/induk', [EmployeeController::class, 'getNoInduk'])->name('api.employee.getNoInduk');
Route::get('employee/nomor/surat', [EmployeeController::class, 'getNoSurat'])->name('api.employee.getNoSurat');

//Purchase
Route::group(['namespace' => 'Purchase','prefix'=>'purchase'], function() {
    Route::resource('order', 'OrderController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);

    Route::resource('description', 'DescriptionOrderController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
});

// Inventory
Route::group(['namespace' => 'Inventory','prefix'=>'inventory'], function() {
    Route::resource('receipt', 'ReceiptController', ['only' => [
        'index','store'
    ]]);
    Route::get('receipts/number/{id}', [App\Http\Controllers\Inventory\ReceiptController::class, 'getNumberReceipt']);
    Route::get('receipts/item/{product_id}', [App\Http\Controllers\Inventory\ReceiptController::class, 'get_items_by_product']);
    Route::get('receipts/product/serial_number', [App\Http\Controllers\Inventory\ReceiptController::class, 'product_serial_number']);

    Route::resource('serial_number', 'SerialNumberController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
    Route::post('serial_numbers/bpb', [App\Http\Controllers\Inventory\SerialNumberController::class, 'storeBpb']);
    Route::get('serial_numbers/products/{product_id}', [App\Http\Controllers\Inventory\SerialNumberController::class, 'getDataNoseriByProduct']);
    Route::get('serial_numbers/detail/{type}', [App\Http\Controllers\Inventory\SerialNumberController::class, 'getDataNoseri']);
    Route::patch('serial_numbers/detail/{id}', [App\Http\Controllers\Inventory\SerialNumberController::class, 'updateNoseri']);

    Route::resource('request_item', 'RequestItemController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
    Route::get('request_item/number/{branch_id}/{type_id}', [App\Http\Controllers\Inventory\RequestItemController::class, 'getAutoNumber']);
    Route::get('request_items/getData', [App\Http\Controllers\Inventory\RequestItemController::class, 'getData']);
    Route::get('request_items/approve/{id}', [App\Http\Controllers\Inventory\RequestItemController::class, 'approve']);
    Route::get('request_items/reject/{id}', [App\Http\Controllers\Inventory\RequestItemController::class, 'reject']);
    Route::get('request_items/close/{id}', [App\Http\Controllers\Inventory\RequestItemController::class, 'close']);

    Route::resource('product_expenditure', 'ProductExpenditureController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
    Route::get('product_expenditures/getData', [App\Http\Controllers\Inventory\ProductExpenditureController::class, 'getData']);
    Route::get('product_expenditure/number/{branch_id}/{type_id}', [App\Http\Controllers\Inventory\ProductExpenditureController::class, 'getAutoNumber']);
    Route::get('product_expenditures/return', [App\Http\Controllers\Inventory\ProductExpenditureController::class, 'get_data_return']);
    Route::get('product_expenditures/serial', [App\Http\Controllers\Inventory\ProductExpenditureController::class, 'get_data_serial']);

    Route::resource('receipt_not_purchase', 'ReceiptNotPurchaseController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
    Route::get('receipt_not_purchase/number/{branch_id}/{type_id}', [App\Http\Controllers\Inventory\ReceiptNotPurchaseController::class, 'getAutoNumber']);
    Route::get('receipt_not_purchase/data/return', [App\Http\Controllers\Inventory\ReceiptNotPurchaseController::class, 'get_data_return']);
    Route::get('receipt_not_purchase/data/serial', [App\Http\Controllers\Inventory\ReceiptNotPurchaseController::class, 'get_data_serial']);

    Route::resource('return_item', 'ReturnItemController', ['only' => [
        'index','show', 'store', 'update', 'destroy'
    ]]);
    Route::get('return_item/number/{branch_id}', [App\Http\Controllers\Inventory\ReturnItemController::class, 'getAutoNumber']);
});

Route::get('/purchase/orders/search', [App\Http\Controllers\Purchase\OrderController::class, 'getData'])->name('api.purchase.order.search');
Route::get('/purchase/orders/ttb', [App\Http\Controllers\Purchase\OrderController::class, 'getDataTtb'])->name('api.purchase.order.ttb');
Route::get('/purchase/orders/approve/{id}', [App\Http\Controllers\Purchase\OrderController::class, 'approve'])->name('api.purchase.order.approve');
Route::get('/purchase/orders/approve/reject/{id}', [App\Http\Controllers\Purchase\OrderController::class, 'reject_approve'])->name('api.purchase.order.reject_approve');
Route::get('/purchase/orders/release/{id}', [App\Http\Controllers\Purchase\OrderController::class, 'release'])->name('api.purchase.order.release');
Route::get('/purchase/orders/release/reject/{id}', [App\Http\Controllers\Purchase\OrderController::class, 'reject_release'])->name('api.purchase.order.reject_release');
Route::get('/purchase/orders/close/{id}', [App\Http\Controllers\Purchase\OrderController::class, 'close'])->name('api.purchase.order.close');
Route::get('/purchase/orders/description', [App\Http\Controllers\Purchase\OrderController::class, 'description'])->name('api.purchase.order.description');
Route::get('/purchase/orders/number/{id}', [App\Http\Controllers\Purchase\OrderController::class, 'getNumberOP'])->name('api.purchase.order.number');
Route::get('purchase/search', [PurchaseController::class, 'getData'])->name('api.purchase.getData');
Route::get('purchase/item', [PurchaseController::class, 'getItemPurchase'])->name('api.purchase.getItemPurchase');
Route::get('purchase/close/{id}', [PurchaseController::class, 'close'])->name('api.purchase.close');
Route::get('purchase/number/{id}', [PurchaseController::class, 'getNumberPP'])->name('api.purchase.number');
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

//Receipt
// Route::post('purchase/{purchase}/close-purchase', [PurchaseController::class, 'closePurchaseLetter'])->name('api.purchase.closePurchaseLetter');
