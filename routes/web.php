<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hr\EmployeeController;

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



Auth::routes();
Route::get('/', 'PagesController@index');
Route::get('identity/list', [EmployeeController::class, 'IdentityCardList'])->name('employee.IdentityCardList');
Route::get('identity/list/json', [EmployeeController::class, 'getIdentityCard'])->name('employee.getIdentityCard');

Route::get('employee/list', [EmployeeController::class, 'IdentityCardList'])->name('employee.IdentityCardList');

// Demo routes
Route::get('/datatables', 'PagesController@datatables');
Route::get('/spp', 'PagesController@spp');
Route::get('/select2', 'PagesController@select2');
Route::get('/jquerymask', 'PagesController@jQueryMask');
Route::get('/icons/custom-icons', 'PagesController@customIcons');
Route::get('/icons/flaticon', 'PagesController@flaticon');
Route::get('/icons/fontawesome', 'PagesController@fontawesome');
Route::get('/icons/lineawesome', 'PagesController@lineawesome');
Route::get('/icons/socicons', 'PagesController@socicons');
Route::get('/icons/svg', 'PagesController@svg');

// Quick search dummy route to display html elements in search dropdown (header search)
Route::get('/quick-search', 'PagesController@quickSearch')->name('quick-search');




// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
