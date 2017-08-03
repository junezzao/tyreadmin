<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'Auth\AuthController@getLogin');

Route::get('/env', function () {
    return app()->environment();
});
Route::get('/pwd/{password}', function ($password) {
    return bcrypt($password);
});

// Login
Route::group(['prefix' => 'auth'], function () {

    Route::get('login', array('uses' => 'Auth\AuthController@getLogin', 'as' => 'login'));
    Route::post('login', 'Auth\AuthController@postLogin');

    Route::get('logout', array('uses' => 'Auth\AuthController@getLogout', 'as' => 'logout' ));
    Route::post('resetPassword', array('as' => 'resetPassword', 'uses' => 'Auth\AuthController@resetPassword'));

    // Registration
    Route::get('register', 'Auth\RegisterController@getRegister');
    Route::post('register', 'Auth\RegisterController@postRegister');
});
Route::get('captcha/refresh','Auth\AuthController@captchaRefresh');
Route::post('password/forgot','Auth\AuthController@forgot');
// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getResetPassword');
// Route::get('password/reset', array('uses' => 'UsersController@showVerify', 'as' => 'users.show_verify'));
Route::post('password/reset', 'Auth\PasswordController@postReset');

Route::get('account/activate/{token}', 'Auth\PasswordController@getActivateAccount');

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'data'], function () {
        Route::post('/upload', 'DataController@upload')->name('data.upload');
        Route::post('/download/template', 'DataController@downloadTemplate')->name('data.download.template');
        Route::get('/list', 'DataController@list')->name('data.list');
        Route::get('/print/diagnostic', 'DataController@printDiagnostic')->name('data.print.diagnostic');
    });
    Route::resource('data', 'DataController');

    Route::resource('history', 'HistoryController');

    Route::group(['prefix' => 'reports'], function () {
        Route::get('/serial_no_analysis', 'ReportController@serialNoAnalysis')->name('reports.serialNoAnalysis');
        Route::get('/odometer_analysis', 'ReportController@odometerAnalysis')->name('reports.odometerAnalysis');
        Route::get('/tyre_removal_mileage', 'ReportController@tyreRemovalMileage')->name('reports.tyreRemovalMileage');
        Route::get('/tyre_removal_record', 'ReportController@tyreRemovalRecord')->name('reports.tyreRemovalRecord');
        Route::get('/truck_tyre_cost', 'ReportController@truckTyreCost')->name('reports.truckTyreCost');
        Route::get('/truck_service_record', 'ReportController@truckServiceRecord')->name('reports.truckServiceRecord');
    });

    Route::resource('reports', 'ReportController');

    Route::get('verify', array('uses' => 'UsersController@showVerify'));
    Route::get('account_details', array('uses' => 'UsersController@getAccountDetails', 'as' => 'user.account_details'));
    Route::post('account_details', 'UsersController@updateAccountDetails');
    Route::post('users/verify/', array('uses' => 'UsersController@verify', 'as' => 'users.verify'));
    
    Route::group(['prefix' => 'user'], function () {
        Route::get('subscription/{user_id}', array('uses' => 'UsersController@subscription', 'as' => 'user.subscription'));
        Route::post('subscribe/{user_id', array('uses' => 'UsersController@subscribe', 'as' => 'user.subscribe'));
        Route::get('change_password/{user_id}', array('uses' => 'UsersController@changePassword', 'as' => 'user.changePassword'));
        Route::put('change_password/{user_id}', array('uses' => 'UsersController@changePasswordSubmit', 'as' => 'user.changePassword.submit'));
    });
    Route::resource('user','UsersController');
    Route::resource('users','UsersController');

    Route::group(['prefix' => 'admin'], function () {

        Route::get('users/table_data', 'Admin\UsersController@getTableData');
        Route::resource('users', 'Admin\UsersController');

        Route::resource('roles', 'Admin\AccessController');
        Route::get('permissions', array('uses' => 'Admin\UsersController@getPermissions', 'as' => 'admin.permissions'));
	    Route::get('roles/create', 'Admin\UsersController@createRole');
	    Route::post('roles/create', 'Admin\UsersController@storeRole');

        Route::group(['prefix' => 'config', 'middleware' => 'role:administrator|superadministrator'], function() {
            Route::get('/', array('uses' => 'Admin\ConfigurationsController@index', 'as' => 'admin.configurations.index'));
            Route::post('/enable', array('uses' => 'Admin\ConfigurationsController@enableModule', 'as' => 'admin.module.enable'));
            Route::post('/disable', array('uses' => 'Admin\ConfigurationsController@disableModule', 'as' => 'admin.module.disable'));
        });

        Route::resource('roles', 'Admin\AccessController');
    });

});