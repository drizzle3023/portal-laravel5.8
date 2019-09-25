<?php

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

Route::get('/', 'AdminController@index');
Route::get('/login', 'AdminController@index');
Route::post('/login', 'AdminController@doLogin');
Route::get('/logout', 'AdminController@logout');
Route::get('/forgot-password', 'AdminController@showForgotPasswordPage');
Route::post('/reset-password', 'AdminController@resetPassword');

Route::middleware('user-auth')->group(function (){
    Route::get('/dashboard', 'AdminController@dashboard');
    Route::get('/profile', 'AdminController@showProfilePage');
    Route::post('/profile/edit', 'AdminController@editProfile');

    Route::prefix('products')->group(function () {
        Route::get('/', 'AdminController@showProductsPage');
    });

    Route::prefix('domains')->group(function () {
        Route::get('/', 'AdminController@showDomainPage');
        Route::get('/check/{id}', 'AdminController@checkDomain');
    });

    Route::prefix('statistics')->group(function () {
        Route::get('/', 'AdminController@showStatisticsPage');
    });

    Route::prefix('search')->group(function () {
        Route::get('/', 'AdminController@showSearchPage');
    });

    Route::prefix('whitelist')->group(function () {
        Route::get('/', 'AdminController@showWhitelistPage');
        Route::get('/add', 'AdminController@showAddWhitelistPage');
        Route::post('/add', 'AdminController@addWhitelist');
        Route::get('/edit/{id}', 'AdminController@showEditWhitelistPage');
        Route::post('/edit', 'AdminController@editWhitelist');
        Route::post('/delete', 'AdminController@deleteWhitelist');
        Route::post('/toggle-enable', 'AdminController@toggleWhitelistEnable');
    });

    Route::prefix('blacklist')->group(function () {
        Route::get('/', 'AdminController@showBlacklistPage');
        Route::get('/add', 'AdminController@showAddBlacklistPage');
        Route::post('/add', 'AdminController@addBlacklist');
        Route::get('/edit/{id}', 'AdminController@showEditBlacklistPage');
        Route::post('/edit', 'AdminController@editBlacklist');
        Route::post('/delete', 'AdminController@deleteBlacklist');
        Route::post('/toggle-enable', 'AdminController@toggleBlacklistEnable');
    });

});

Route::middleware('admin-auth')->group(function (){
    Route::prefix('employees')->group(function () {

        Route::get('/', 'AdminController@showEmployeesPage');
        Route::get('/add', 'AdminController@showEmployeeAddPage');
        Route::post('/add', 'AdminController@addEmployee');
        Route::get('/edit/{id}', 'AdminController@showEmployeeEditPage');
        Route::post('/edit', 'AdminController@editEmployee');
        Route::post('/del', 'AdminController@delEmployee');
        Route::post('/toggle-enable', 'AdminController@toggleEmployeeEnable');
    });

    Route::prefix('customers')->group(function () {

        Route::get('/add', 'AdminController@showCustomerAddPage');
        Route::post('/add', 'AdminController@addCustomer');
        Route::get('/edit/{id}', 'AdminController@showCustomerEditPage');
        Route::post('/edit', 'AdminController@editCustomer');
        Route::post('/del', 'AdminController@delCustomer');
        Route::post('/toggle-enable', 'AdminController@toggleCustomerEnable');
        Route::get('/print-invoice/{id}', 'AdminController@showCustomerInvoicePrintPreviewPage');
        Route::get('/print-invoice/{id}/print', 'AdminController@printCustomerInvoice');
        Route::post('/resuscitate-customer', 'AdminController@resuscitateCustomer');

    });
});
