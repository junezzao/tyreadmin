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

    Route::get('dashboard', array('uses' => 'DashboardController@index', 'as' => 'main.dashboard'));
    Route::get('changelog/export', array('uses' => 'ChangelogController@export', 'as' => 'changelog.export'));
    Route::resource('changelog', 'ChangelogController');

    Route::get('brands/table_data', array('uses' => 'BrandsController@getTableData',  'as' => 'brands.tableData'));
    Route::get('brands/archived', array('uses' => 'BrandsController@archived',  'as' => 'brands.archived'));
    Route::get('brands/{merchantId}/byMerchant', array('uses' => 'BrandsController@getByMerchant', 'as' => 'brands.byMerchant'));
    Route::resource('brands', 'BrandsController');
    
    Route::post('download_csv', array('uses' => 'Controller@downloadCsvFile',  'as' => 'downloadCsv'));

    Route::group(['module' => 'contracts'], function () {
        Route::group(['prefix' => 'contracts'], function () {
            Route::post('{id}/update-dates', array('uses' => 'ContractsController@updateDate', 'as' => 'contract.updateDates'));
            Route::get('table_data', array('uses' => 'ContractsController@getTableData', 'as' => 'contracts.getTableData'));
            Route::get('{id}/duplicate', array('uses' => 'ContractsController@duplicate', 'as' => 'contracts.duplicate'));
            Route::group(['prefix' => 'channels'], function () {
                Route::post('{id}/update-dates', array('uses' => 'ChannelContractsController@updateDate', 'as' => 'contracts.channels.updateDates'));
                Route::get('table_data', array('uses' => 'ChannelContractsController@getTableData', 'as' => 'contracts.channels.getTableData'));
                Route::get('{id}/duplicate', array('uses' => 'ChannelContractsController@duplicate', 'as' => 'contracts.channels.duplicate'));
            });
            Route::resource('channels', 'ChannelContractsController');
        });
        Route::resource('contracts', 'ContractsController');
        Route::get('contract_calculator', array('uses' => 'ContractsController@contractCalculator', 'as' => 'contracts.contract_calculator'));
        Route::post('contract_calculator', array('uses' => 'ContractsController@calculate', 'as' => 'contracts.calculate'));
        Route::post('calculate/export', array('uses' => 'ContractsController@exportFeeReport', 'as' => 'contracts.calculate.export'));
    });

    Route::group(['prefix' => 'products', 'module' => 'products'], function () {


        Route::group(['prefix' => 'stock_transfer'], function () {
             //Route::get('{stock_transfer_id}', array('uses' => 'StockTransferController@show', 'as' => 'products.stock_transfer.show'));

            Route::post('create/upload/{merchant_id}',['uses'=>'StockTransferController@postUpload','as' => 'stock-transfer.upload']);Route::post('process', array('uses' => 'StockTransferController@preprocessCreateAndTransfer', 'as' => 'products.stock_transfer.process'));
            Route::get('table_data', array('uses' => 'StockTransferController@getTableData', 'as' => 'products.stock_transfer.tableData'));
            Route::post('{id}/transfer', array('uses' => 'StockTransferController@transfer', 'as' => 'products.stock_transfer.transfer'));
            Route::post('{id}/receive', array('uses' => 'StockTransferController@receive', 'as' => 'products.stock_transfer.receive'));
            Route::get('batch/{batch_id}/merchant/{merchant_id}/channel/{channel_id}', array('uses' => 'StockTransferController@getBatchById', 'as' => 'products.stock_transfer.getBatch'));
            Route::get('add_items/{channel_id}/merchant/{merchant_id}', array('uses' => 'StockTransferController@addItemsModal', 'as' => 'products.stock_transfer.addItemsModal'));
            Route::get('channel/{channel_id}/product/{product_id}', array('uses' => 'StockTransferController@getChannelSkus', 'as' => 'products.stock_transfer.getChannelSkus'));
            Route::get('channel/{channel_id}/merchant/{merchant_id}/search' , array('as'=>'products.stock_transfer.addItemsModal.search', 'uses'=>'StockTransferController@searchDatatable'));
            Route::get('manifests/search',['uses'=>'ManifestController@search','as'=>'products.manifests.search']);
            Route::post('manifests/pickup', array('uses' => 'ManifestController@pickUpManifest', 'as' => 'products.manifests.pickup'));
            Route::get('manifests/{id}/items', array('uses' => 'ManifestController@pickingItems', 'as' => 'products.manifests.items'));
            Route::get('manifests/{id}/export', array('uses' => 'ManifestController@export', 'as' => 'products.manifests.export'));
            Route::post('manifests/{id}/completed', array('uses' => 'ManifestController@completed', 'as' => 'products.manifests.completed'));
            Route::post('manifests/{id}/cancel', array('uses' => 'ManifestController@cancel', 'as' => 'products.manifests.cancel'));
            Route::post('manifests/{id}/pick', array('uses' => 'ManifestController@pickItem', 'as' => 'products.manifests.item.pick'));
            Route::post('manifests/{id}/outofstock', array('uses' => 'ManifestController@outOfStock', 'as' => 'products.manifests.item.outofstock'));
            Route::post('{id}/assign_user', array('uses' => 'ManifestController@assignUser', 'as' => 'products.manifests.assignUser'));
            Route::get('{id}/export',['uses' => 'StockTransferController@export', 'as' => 'products.stock_transfer.export']);

        });
        Route::resource('manifests','ManifestController');
        Route::resource('stock_transfer', 'StockTransferController');

        // Product Management (Create/Restock)
        Route::group(['prefix' => 'create'], function () {
            Route::get('create/table_data', array('uses'=>'ProductController@getTableDataCreate', 'as'=>'products.create.tableData'));

            // Create CRUD
            Route::get('/', array('uses'=>'ProductController@indexCreate','as'=>'products.create.index'));
            Route::get('create', array('uses'=>'ProductController@createCreate','as'=>'products.create.create'));
            Route::post('create', array('uses'=>'ProductController@storeCreate','as'=>'products.create.store'));
            Route::get('{batch_id}', array('uses'=>'ProductController@show','as'=>'products.create.show'));
            Route::get('{batch_id}/edit', array('uses'=>'ProductController@edit','as'=>'products.create.edit'));
        });

        Route::post('{type}/upload', 'ProductController@upload');
        Route::post('download_csv', array('uses'=>'ProductController@downloadCsv','as'=>'products.download.csv'));

        // Shared route
        Route::post('item/{batch_id}/update', array('uses'=>'ProductController@updateItem','as'=>'products.update_item'));
        Route::post('item/{batch_id}/delete', array('uses'=>'ProductController@deleteItem','as'=>'products.delete_item'));
        Route::get('{batch_id}/receive', array('uses'=>'ProductController@receive','as'=>'products.receive'));
        Route::delete('batch/{type}/{batch_id}', array('uses'=>'ProductController@destroy','as'=>'products.destroy'));
        Route::put('batch/{type}/{batch_id}', array('uses'=>'ProductController@update','as'=>'products.update'));

        Route::group(['prefix' => 'restock'], function () {
            Route::get('table_data', array('uses'=>'ProductController@getTableDataRestock', 'as'=>'products.restock.tableData'));

            // Restock CRUD
            Route::get('/', array('uses'=>'ProductController@indexRestock','as'=>'products.restock.index'));
            Route::get('create', array('uses'=>'ProductController@createRestock','as'=>'products.restock.create'));
            Route::post('create', array('uses'=>'ProductController@storeRestock','as'=>'products.restock.store'));
            Route::get('{batch_id}', array('uses'=>'ProductController@show','as'=>'products.restock.show'));
            Route::get('{batch_id}/edit', array('uses'=>'ProductController@edit','as'=>'products.restock.edit'));
        });

        Route::get('get_barcode_csv/{id}', array('uses'=>'ProductController@getBarcodeCsv', 'as' => 'products.get_barcode_csv'));
        Route::get('generate_barcode_csv/{id}', array('uses'=>'ProductController@generateBarcodeCsv', 'as' => 'products.generate_barcode_csv'));

        //Product Iventory
        Route::group(['prefix' => 'inventory'], function () {
            Route::post('export',['uses'=>'InventoryController@export', 'as'=>'inventory.export']);
            Route::get('search', array('uses' => 'InventoryController@searchProductInventory', 'as' => 'inventory.search'));
            Route::post('reject/manage', array('uses' => 'InventoryController@createRejectProducts', 'as' => 'inventory.reject.create'));
            Route::post('reject', array('uses' => 'InventoryController@storeRejectProducts', 'as' => 'inventory.reject.store'));
            Route::post('delete/manage', array('uses' => 'InventoryController@createDeleteProducts', 'as' => 'inventory.delete.create'));
            Route::post('delete', array('uses' => 'InventoryController@storeDeleteProducts', 'as' => 'inventory.delete.store'));
            Route::post('bulk_update',array('uses'=>'InventoryController@showBulkUpdate','as'=>'inventory.bulk_update'));
            Route::post('bulk_update/load',array('uses'=>'InventoryController@bulkLoad','as'=>'inventory.bulk_update.load'));
            Route::post('bulk_update/save',array('uses'=>'InventoryController@bulkSave','as'=>'inventory.bulk_update.save'));
            Route::post('{id}/upload', array('uses'=>'InventoryController@uploadProductMedia', 'as' => 'inventory.product.upload'));
            Route::post('{id}/updateImgOrder', array('uses'=>'InventoryController@updateProductImgOrder', 'as' => 'inventory.product.updateImgOrder'));
            Route::post('{id}/syncImages', array('uses'=>'InventoryController@syncImages', 'as' => 'inventory.product.syncImages'));
            Route::post('{id}/deleteImg', array('uses'=>'InventoryController@deleteProductImg', 'as' => 'inventory.product.deleteImg'));
            Route::post('{id}/setDefaultImg', array('uses'=>'InventoryController@setDefaultProductImg', 'as' => 'inventory.product.setDefaultImg'));
            Route::post('barcode/prepare',array('uses'=>'InventoryController@showBarcode','as'=>'inventory.print_barcode'));
            Route::post('barcode/download',array('uses'=>'InventoryController@downloadBarcode','as'=>'inventory.download_barcode'));
            Route::get('{brandId}/byBrand', array('uses' => 'InventoryController@getProductsByBrand', 'as' => 'inventory.byBrand'));
        });
        Route::get('inventory/{product_id}/edit', array('uses'=>'InventoryController@edit', 'as'=>'inventory.edit'));
        Route::resource('inventory', 'InventoryController');

        Route::get('get_product_details', 'ProductController@getProductDetails');
        Route::resource('categories','CategoriesController');
    });

    Route::get('verify', array('uses' => 'UsersController@showVerify'));
    Route::get('account_details', array('uses' => 'UsersController@getAccountDetails', 'as' => 'user.account_details'));
    Route::post('account_details', 'UsersController@updateAccountDetails');
    Route::post('users/verify/', array('uses' => 'UsersController@verify', 'as' => 'users.verify'));
    Route::resource('users','UsersController');
   	//TEST ROUTE
	Route::get('testupload', array('uses' => 'TestController@index','as' => 'test.dashboard'));
	Route::post('upload', 'TestController@upload');
	Route::post('uploads', 'TestController@uploads');
    Route::post('deleteupload', 'TestController@deleteMedia');
	//TEST ROUTE END

    Route::group(['module' => 'channels'], function () {
        Route::group(['prefix' => 'channels'], function () {

            // custom fields routes
            Route::get('custom_fields/{id}', array('uses' => 'Admin\ChannelController@getCF', 'as' => 'custom_fields.get'));
            Route::post('custom_fields/{id}/update', array('uses' => 'Admin\ChannelController@updateCF', 'as' => 'custom_fields.update'));
            Route::post('custom_fields/{id}/delete', array('uses' => 'Admin\ChannelController@deleteCF', 'as' => 'custom_fields.delete'));

            Route::get('{channel_id}/getorder/{order_code}', 'Admin\ChannelController@getOrder');

            Route::group(['prefix' => 'inventory'], function () {
                // assign categories routes
                Route::post('categories',array('uses'=>'ChannelInventoryController@bulkUpdateCategories','as'=>'channel.inventory.categories'));
                Route::get('categories/load',array('uses'=>'ChannelInventoryController@bulkLoadCategories','as'=>'channel.inventory.categories.load'));
                Route::post('categories/save',array('uses'=>'ChannelInventoryController@bulkSaveCategories','as'=>'channel.inventory.categories.save'));

                // bulk update custom fields and channel sku
                Route::post('bulk_update',array('uses'=>'ChannelInventoryController@showBulkUpdate','as'=>'channel.inventory.bulk_update'));
                Route::post('bulk_update/load',array('uses'=>'ChannelInventoryController@bulkLoad','as'=>'channel.inventory.bulk_update.load'));
                Route::post('bulk_update/save',array('uses'=>'ChannelInventoryController@bulkSave','as'=>'channel.inventory.bulk_update.save'));

                Route::get('search', array('uses' => 'ChannelInventoryController@searchChannelInventory', 'as' => 'channel.inventory.search'));

                Route::post('sync_products/{type}', array('uses' => 'ChannelInventoryController@syncProducts', 'as' => 'channel.inventory.sync_products'));

                Route::get('generate_product_list/{channelId}', array('uses' => 'ChannelInventoryController@generateProductListCsv', 'as' => 'channel.inventory.get_product_list_csv'));
            });

            Route::get('inventory/{product_id}/edit', array('uses'=>'ChannelInventoryController@edit', 'as'=>'channels.inventory.edit'));
            Route::resource('inventory', 'ChannelInventoryController');
        });
    });

    Route::group(['prefix' => 'admin'], function () {

        // Testing Routes
        Route::group(['prefix' => 'testing'], function () {
            Route::get('run_syncs', array('uses' => 'TestController@runSyncs', 'as' => 'admin.testing.run_syncs'));
            Route::get('show_admin_error_log', array('uses' => '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index', 'as' => 'admin.testing.show_admin_error_log'));
            Route::get('show_hapi_logs', array('uses' => 'TestController@getHapiLogs', 'as' => 'admin.testing.show_hapi_logs'));
            Route::get('generate_stats', array('uses' => 'TestController@generateDashboardStats', 'as' => 'admin.testing.generate_stats'));
            Route::get('phpinfo', array('uses' => 'TestController@showPhpinfo', 'as' => 'admin.testing.phpinfo'));
            Route::get('stock_movement/{by}/{id}', array('uses' => 'TestController@getStockMovements', 'as' => 'admin.testing.stock_movement'));
        });

        Route::resource('testing', 'TestController');
        //END

        Route::get('users/table_data', 'Admin\UsersController@getTableData');
        Route::resource('users', 'Admin\UsersController');

        Route::resource('roles', 'Admin\AccessController');
        Route::get('permissions', array('uses' => 'Admin\UsersController@getPermissions', 'as' => 'admin.permissions'));
	    Route::get('roles/create', 'Admin\UsersController@createRole');
	    Route::post('roles/create', 'Admin\UsersController@storeRole');
        Route::get('merchants/{channelId}/byChannel','Admin\MerchantsController@getMerchantsByChannel');
	    Route::get('merchants/table_data/', 'Admin\MerchantsController@getTableData');
        Route::get('merchants/{channelId}/byChannel','Admin\MerchantsController@getMerchantsByChannel');
        Route::get('merchants/{slug}/delete','Admin\MerchantsController@destroy');
        Route::resource('merchants', 'Admin\MerchantsController');

        Route::get('suppliers/table_data', 'Admin\SuppliersController@getTableData');
        Route::get('suppliers/merchant/{merchantId}', array('uses' => 'Admin\SuppliersController@getSupplierByMerchant', 'as' => 'admin.suppliers.byMerchant'));
        Route::resource('suppliers', 'Admin\SuppliersController');

        Route::get('issuing_companies/table_data', array('uses' => 'Admin\IssuingCompanyController@getTableData', 'as' => 'admin.issuing-companies.table_data'));
        Route::resource('issuing_companies', 'Admin\IssuingCompanyController');

        // Channels route
        Route::group(['module' => 'channels'], function () {
            Route::group(['prefix' => 'channels'], function () {
                Route::get('/channels_table_data', array('uses' => 'Admin\ChannelController@getChannelsTableData', 'as' => 'admin.channels.table_data'));
                Route::get('/{id}/deactivate', array('uses' => 'Admin\ChannelController@deactivate', 'as' => 'admin.channels.deactivate'));
                Route::get('/{id}/channel_type_fields', array('uses' => 'Admin\ChannelController@getChannelTypeFields', 'as' => 'admin.channels.channel_type_fields'));
                Route::get('/merchant/{merchant_id}', array('uses' => 'Admin\ChannelController@getChannelsByMerchant', 'as' => 'admin.merchant.channels'));
                Route::get('/merchant/{merchant_id}/channel_type/{channel_type_id}', array('uses' => 'Admin\ChannelController@getChannelsByMerchantAndType', 'as' => 'admin.merchant.channeltype.channels'));
                Route::get('/{id}/set-storefrontapi', array('uses' => 'Admin\ChannelController@setStorefrontApi', 'as' => 'admin.channels.setstorefrontapi'));

                Route::group(['prefix' => 'sync_history'], function () {
                    Route::get('/', array('uses' => 'Admin\ChannelController@syncHistoryIndex', 'as' => 'admin.channels.sync_history.index'));
                    Route::get('/archive', array('uses' => 'Admin\ChannelController@syncArchiveIndex', 'as' => 'admin.channels.sync_history.archive'));
                    Route::get('/get_sync_history', array('uses' => 'Admin\ChannelController@getSyncHistory', 'as' => 'admin.channels.sync_history.data'));
                    Route::post('/retry/{sync_id}', array('uses' => 'Admin\ChannelController@retrySync', 'as' => 'admin.channels.sync_history.retry'));
                    Route::post('/cancel/{sync_id}', array('uses' => 'Admin\ChannelController@cancelSync', 'as' => 'admin.channels.sync_history.cancel'));
                    Route::post('/bulkUpdate', array('uses' => 'Admin\ChannelController@bulkUpdateSyncs', 'as' => 'admin.channels.sync_history.bulkUpdate'));
                });

                Route::post('/register_webhooks/{channel_id}', array('uses' => 'Admin\ChannelController@registerWebhooks', 'as' => 'admin.channels.register_webhooks'));
                Route::post('/import_store_categories/{channel_id}', array('uses' => 'Admin\ChannelController@importStoreCategories', 'as' => 'admin.channels.import_store_categories'));
                Route::post('/{id}/getShippingProvider', array('uses' => 'Admin\ChannelController@getShippingProvider', 'as' => 'admin.channels.getShippingProvider'));
            });
            Route::resource('channels', 'Admin\ChannelController');

            // Channel Type
            Route::group(['prefix' => 'channel-type'], function () {
                Route::get('/{id}/deactivate', array('uses' => 'Admin\ChannelTypeController@deactivate', 'as' => 'admin.channel-type.deactivate'));
                Route::get('/channel_type_table_data', array('uses' => 'Admin\ChannelTypeController@getChannelTypesTableData', 'as' => 'admin.channel-type.table_data'));

                // Third Party Categories
                Route::group(['prefix' => 'categories'], function () {
                    Route::get('/download_products_with_outdated_product/{channel_type_id}', array('uses' => 'Admin\CategoriesController@downloadProductsWithOutdatedCategory', 'as' => 'admin.categories.download_products_with_outdated_category'));
                    Route::post('{channel_type_id}/update', array('uses' => 'Admin\CategoriesController@update', 'as' => 'admin.categories.update'));
                    Route::put('/remap/{channel_type_id}', array('uses' => 'Admin\CategoriesController@remap', 'as' => 'admin.categories.remap'));
                });
                Route::resource('categories', 'Admin\CategoriesController');
            });
            Route::resource('channel-type', 'Admin\ChannelTypeController');
        });
        // END channels route

        Route::group(['prefix' => 'fulfillment', 'module' => 'fulfillment'], function () {
            Route::get('return/{return_id}/reject', array('uses' => 'Admin\ReturnController@reject', 'as' => 'admin.fulfillment.return.reject'));
            Route::get('return/search', array('uses' => 'Admin\ReturnController@search', 'as' => 'admin.fulfillment.return.search'));
            Route::get('return/channel/{channel_id?}', array('uses' => 'Admin\ReturnController@index'), function ($channel_id = null) {
                return $channel_id;
            });
            Route::resource('return', 'Admin\ReturnController');

            Route::group(['prefix' => 'failed_orders'], function(){
                Route::get('/', array('uses' => 'Admin\FailedOrdersController@index', 'as' => 'admin.fulfillment.failed_orders.index'));
                Route::get('/{id}/discard', array('uses' => 'Admin\FailedOrdersController@discard', 'as' => 'admin.fulfillment.failed_orders.discard'));
                Route::get('/{id}/create_order', array('uses' => 'Admin\FailedOrdersController@createOrder', 'as' => 'admin.fulfillment.failed_orders.create_order'));
                Route::get('/getTableData', array('uses' => 'Admin\FailedOrdersController@getTableData', 'as' => 'admin.fulfillment.failed_orders.getTableData'));
            });

            Route::group(['prefix' => 'manifests'], function () {
                Route::get('count', array('uses' => 'Admin\ManifestController@count', 'as' => 'admin.fulfillment.manifests.count'));
                Route::get('search/{type?}', array('uses' => 'Admin\ManifestController@search', 'as' => 'admin.fulfillment.manifests.search'));
                Route::get('generate', array('uses' => 'Admin\ManifestController@generate', 'as' => 'admin.fulfillment.manifests.generate'));
                Route::post('pickup', array('uses' => 'Admin\ManifestController@pickUpManifest', 'as' => 'admin.fulfillment.manifests.pickup'));
                Route::get('{id}/items', array('uses' => 'Admin\ManifestController@pickingItems', 'as' => 'admin.fulfillment.manifests.items'));
                Route::post('{id}/completed', array('uses' => 'Admin\ManifestController@completed', 'as' => 'admin.fulfillment.manifests.completed'));
                Route::post('{id}/pick', array('uses' => 'Admin\ManifestController@pickItem', 'as' => 'admin.fulfillment.manifests.item.pick'));
                Route::post('{id}/outofstock', array('uses' => 'Admin\ManifestController@outOfStock', 'as' => 'admin.fulfillment.manifests.item.outofstock'));
                Route::get('{id}/orders', array('uses' => 'Admin\ManifestController@getUniqueOrders', 'as' => 'admin.fulfillment.manifests.orders'));
                Route::get('{id}/export_pos_laju', array('uses' => 'Admin\ManifestController@exportPosLaju', 'as' => 'admin.fulfillment.manifests.exportPosLaju'));
                Route::get('{id}/print_documents', array('uses' => 'Admin\ManifestController@printDocuments', 'as' => 'admin.fulfillment.manifests.printDocuments'));
                Route::post('{id}/assign_user', array('uses' => 'Admin\ManifestController@assignUser', 'as' => 'admin.fulfillment.manifests.assignUser'));
            });
            Route::resource('manifests', 'Admin\ManifestController');
        });

        Route::group(['prefix' => 'reports'], function () {
            Route::get('/', array('uses' => 'Admin\ReportsController@index', 'as' => 'admin.reports.index'));
            Route::post('/', array('uses' => 'Admin\ReportsController@search', 'as' => 'admin.reports.search'));
            Route::get('/third_party_reports', array('uses' => 'Admin\ThirdPartyReportController@index', 'as' => 'admin.tp_reports.index'));
            Route::get('/download_tp_template', array('uses' => 'Admin\ThirdPartyReportController@downloadTemplate', 'as' => 'admin.reports.download_tp_template'));
            Route::post('/tp_reports/upload', array('uses' => 'Admin\ThirdPartyReportController@upload', 'as' => 'admin.reports.tp_reports.upload'));
            Route::get('/third_party_reports/search', array('uses' => 'Admin\ThirdPartyReportController@search', 'as' => 'admin.tp_reports.search'));
            Route::get('/third_party_reports/counters', array('uses' => 'Admin\ThirdPartyReportController@counters', 'as' => 'admin.tp_reports.counters'));
            Route::post('/third_party_reports/complete_verified_orders', array('uses' => 'Admin\ThirdPartyReportController@completeVerifiedOrders', 'as' => 'admin.tp_reports.complete_verified_orders'));
            Route::post('/third_party_reports/complete_verified_order_items', array('uses' => 'Admin\ThirdPartyReportController@completeVerifiedOrderItems', 'as' => 'admin.tp_reports.complete_verified_order_items'));
            Route::post('/third_party_reports/export', array('uses' => 'Admin\ThirdPartyReportController@export', 'as' => 'admin.tp_reports.export'));
            Route::post('/third_party_reports/discard', array('uses' => 'Admin\ThirdPartyReportController@discard', 'as' => 'admin.tp_reports.discard'));
            Route::post('/third_party_reports/verify', array('uses' => 'Admin\ThirdPartyReportController@bulk_verify', 'as' => 'admin.tp_reports.bulk_verify'));
            Route::post('/third_party_reports/moveTo', array('uses' => 'Admin\ThirdPartyReportController@bulk_moveTo', 'as' => 'admin.tp_reports.bulk_moveTo'));
            Route::get('/third_party_reports/{id}', array('uses' => 'Admin\ThirdPartyReportController@show', 'as' => 'admin.tp_reports.show'));
            Route::get('/third_party_reports/{id}/edit', array('uses' => 'Admin\ThirdPartyReportController@edit', 'as' => 'admin.tp_reports.edit'));
            Route::post('/third_party_reports/{id}/update', array('uses' => 'Admin\ThirdPartyReportController@update', 'as' => 'admin.tp_reports.update'));
            Route::delete('/third_party_reports/{id}/destroy', array('uses' => 'Admin\ThirdPartyReportController@destroy', 'as' => 'admin.tp_reports.destroy'));
            Route::get('/third_party_reports/{id}/print', array('uses' => 'Admin\ThirdPartyReportController@print', 'as' => 'admin.tp_reports.print'));
            Route::get('/third_party_reports/{id}/verify', array('uses' => 'Admin\ThirdPartyReportController@verify', 'as' => 'admin.tp_reports.verify'));
            Route::post('/third_party_reports/{id}/addRemark', array('uses' => 'Admin\ThirdPartyReportController@addRemark', 'as' => 'admin.tp_reports.addRemark'));
            Route::post('/third_party_reports/generateReport', array('uses' => 'Admin\ThirdPartyReportController@generateReport', 'as' => 'admin.tp_reports.generateReport'));
            Route::post('/third_party_reports/complete_verified_order_items', array('uses' => 'Admin\ThirdPartyReportController@completeVerifiedOrderItems', 'as' => 'admin.tp_reports.complete_verified_order_items'));
            Route::post('/third_party_reports/export', array('uses' => 'Admin\ThirdPartyReportController@export', 'as' => 'admin.tp_reports.export'));
            Route::get('/third_party_reports/num_verified_items', array('uses' => 'Admin\ThirdPartyReportController@verifiedItemsCount', 'as' => 'admin.tp_reports.num_verified_items'));
            Route::post('/third_party_reports/remark/{remarkId}/resolve', array('uses' => 'Admin\ThirdPartyReportController@resolveRemark', 'as' => 'admin.tp_reports.resolve_remark'));
        });
        Route::group(['prefix' => 'generate-report'], function () {
            Route::get('/', array('uses' => 'Admin\ReportsController@generateReportIndex', 'as' => 'admin.generate-report.index'));
            Route::post('/', array('uses' => 'Admin\ReportsController@generateReport', 'as' => 'admin.generate-report.search'));
            Route::post('/export', array('uses' => 'Admin\ReportsController@generateReportExport', 'as' => 'admin.generate-report.export'));
        });

        Route::group(['prefix' => 'config', 'middleware' => 'role:administrator|superadministrator'], function() {
            Route::get('/', array('uses' => 'Admin\ConfigurationsController@index', 'as' => 'admin.configurations.index'));
            Route::post('/enable', array('uses' => 'Admin\ConfigurationsController@enableModule', 'as' => 'admin.module.enable'));
            Route::post('/disable', array('uses' => 'Admin\ConfigurationsController@disableModule', 'as' => 'admin.module.disable'));
        });

        Route::resource('roles', 'Admin\AccessController');

        Route::get('reject/getTableData', 'Admin\RejectController@getTableData');
        Route::resource('reject', 'Admin\RejectController');

    });

    Route::group(['prefix' => 'orders', 'module' => 'fulfillment', 'middleware' => 'role:administrator|superadministrator|accountexec|operationsexec|warehousexec|finance|channelmanager'], function () {
        Route::get('channel/{channel_id?}', 'OrdersController@index', function ($channel_id = null) {
            return $channel_id;
        });
        Route::get('/{order_id}/print', 'OrdersController@showPrint');
        Route::get('/print/return_slip/{order_id}', array('uses' => 'OrdersController@printReturnSlip', 'as' => 'orders.print.return_slip'));
        Route::post('/print/{document_type}/{order_id}', array('uses' => 'OrdersController@printDocument', 'as' => 'orders.print_document'));
        Route::get('/print/order_sheet/{order_id}', array('uses' => 'OrdersController@printOrderSheet', 'as' => 'orders.print.order_sheet'));
        Route::get('/search', array('uses' => 'OrdersController@search', 'as' => 'orders.search'));
        Route::get('/levels', array('uses' => 'OrdersController@countLevels', 'as' => 'orders.level'));
        Route::get('/count', array('uses' => 'OrdersController@countOrders', 'as' => 'orders.count'));
        Route::get('/find_order', array('uses' => 'OrdersController@checkOrder', 'as' => 'orders.find'));
        Route::post('update-status', 'OrdersController@updateOrderStatus');
        Route::post('cancel-order/{order_id}', 'OrdersController@cancelOrder');
        Route::get('create/channel/{channel_id?}', 'OrdersController@create', function ($channel_id = null) {
            return $channel_id;
        });
        Route::get('/create', array('uses' => 'OrdersController@create', 'as' => 'orders.create'));
        Route::post('/create', array('uses' => 'OrdersController@store', 'as' => 'orders.create'));
        Route::get('/{order_id}', array('uses' => 'OrdersController@show', 'as' => 'order.show'));
        Route::post('/{id}/send_consignment_number', array('uses' => 'OrdersController@sendConsignmentNumber'));
        Route::get('/', array('uses' => 'OrdersController@index', 'as' => 'orders.index'));

        Route::get('/{order_id}/item/{item_id}/cancel', array('uses' => 'OrdersController@cancelItem', 'as' => 'order.item.cancel'));
        Route::get('/{order_id}/item/{item_id}/return', array('uses' => 'OrdersController@returnItem', 'as' => 'order.item.return'));
        Route::post('/{order_id}/item/pack', array('uses' => 'OrdersController@packItem', 'as' => 'order.item.pack'));
        Route::post('/{order_id}/item/status-update', array('uses' => 'OrdersController@updateItemStatus', 'as' => 'order.item.updateStatus'));

        /*** Order Notes ***/
        Route::post('/{id}/create-note', array('uses' => 'OrdersController@createNote', 'as' => 'order.notes.create'));
    });


    Route::group(['prefix' => 'member'], function() {
        Route::get('{id}', 'MembersController@show');
    });

    // Routes for channel manager specifically
    Route::group(['prefix' => 'byChannel/{channel_id}'], function() {
        Route::get('brands', array('uses'=>'BrandsController@index', 'as'=>'byChannel.brands.index'));

        Route::group(['prefix' => 'orders'], function () {
            Route::get('/', array('uses'=>'OrdersController@index', 'as'=>'byChannel.orders.index'));
            Route::get('create', array('uses'=>'OrdersController@create', 'as'=>'byChannel.orders.create'));
            Route::get('{order_id}', array('uses'=>'OrdersController@show', 'as'=>'byChannel.orders.show'));
        });

        Route::group(['prefix' => 'products'], function () {
            Route::group(['prefix' => 'create'], function () {
                Route::get('/', array('uses'=>'ProductController@indexCreate', 'as'=>'byChannel.products.create.index'));
                Route::get('{batch_id}', array('uses'=>'ProductController@show', 'as'=>'byChannel.products.create.show'));
            });

            Route::group(['prefix' => 'restock'], function () {
                Route::get('/', array('uses'=>'ProductController@indexRestock', 'as'=>'byChannel.products.restock.index'));
                Route::get('{batch_id}', array('uses'=>'ProductController@show', 'as'=>'byChannel.products.restock.show'));
            });

            Route::group(['prefix' => 'stock_transfer'], function () {
                Route::get('/', array('uses'=>'StockTransferController@index', 'as'=>'byChannel.products.stock_transfer.index'));
                Route::get('{stock_transfer}', array('uses'=>'StockTransferController@show', 'as'=>'byChannel.products.stock_transfer.show'));
            });

            Route::get('inventory', array('uses'=>'InventoryController@index', 'as'=>'byChannel.products.inventory.index'));
        });

        Route::group(['prefix' => 'channels'], function () {
            Route::group(['prefix' => 'inventory'], function () {
                Route::get('/', array('uses'=>'ChannelInventoryController@index', 'as'=>'byChannel.channels.inventory.index'));
                Route::get('{product_id}/edit', array('uses'=>'ChannelInventoryController@edit', 'as'=>'byChannel.channels.inventory.edit'));
                Route::post('bulk_update', array('uses'=>'ChannelInventoryController@showBulkUpdate','as'=>'byChannel.channel.inventory.bulk_update'));
                Route::post('categories', array('uses'=>'ChannelInventoryController@bulkUpdateCategories','as'=>'byChannel.channel.inventory.categories'));
                Route::post('sync_products/{type}', array('uses'=>'ChannelInventoryController@syncProducts', 'as'=>'byChannel.channel.inventory.sync_products'));
            });
        });

        Route::group(['prefix' => 'contracts'], function () {
            Route::group(['prefix' => 'channels'], function () {
                Route::get('/', array('uses'=>'ChannelContractsController@index', 'as'=>'byChannel.contracts.channels.index'));
                Route::get('{id}', array('uses'=>'ChannelContractsController@channelShow', 'as'=>'byChannel.contracts.channels.show'));
                Route::get('table_data', array('uses' => 'ChannelContractsController@getTableData', 'as' => 'byChannel.contracts.channels.getTableData'));
            });
            // Route::resource('channels', 'ChannelContractsController');
        });

        Route::group(['prefix' => 'admin'], function () {
            Route::get('merchants', array('uses'=>'Admin\MerchantsController@index', 'as'=>'byChannel.admin.merchants.index'));
            Route::get('suppliers', array('uses'=>'Admin\SuppliersController@index', 'as'=>'byChannel.admin.suppliers.index'));
            Route::get('fulfillment/return', array('uses'=>'Admin\ReturnController@index', 'as'=>'byChannel.admin.fulfillment.return.index'));
            Route::get('reject', array('uses'=>'Admin\RejectController@index', 'as'=>'byChannel.admin.reject.index'));

            Route::group(['prefix' => 'fulfillment'], function() {
                Route::group(['prefix' => 'failed_orders'], function(){
                    Route::get('/', array('uses' => 'Admin\FailedOrdersController@index', 'as' => 'byChannel.admin.fulfillment.failed_orders.index'));
                    Route::get('/{id}/discard', array('uses' => 'Admin\FailedOrdersController@discard', 'as' => 'byChannel.admin.fulfillment.failed_orders.discard'));
                    Route::get('/{id}/create_order', array('uses' => 'Admin\FailedOrdersController@createOrder', 'as' => 'byChannel.admin.fulfillment.failed_orders.create_order'));
                    Route::get('/getTableData', array('uses' => 'Admin\FailedOrdersController@getTableData', 'as' => 'byChannel.admin.fulfillment.failed_orders.getTableData'));
                });
            });

            Route::group(['prefix' => 'users'], function () {
                Route::get('/', array('uses'=>'Admin\UsersController@index', 'as'=>'byChannel.admin.users.index'));
            });

            Route::group(['prefix' => 'channels'], function () {
                Route::get('/', array('uses'=>'Admin\ChannelController@index', 'as'=>'byChannel.admin.channels.index'));
                Route::get('show', array('uses'=>'Admin\ChannelController@show', 'as'=>'byChannel.admin.channels.show'));
                Route::get('edit', array('uses'=>'Admin\ChannelController@edit', 'as'=>'byChannel.admin.channels.edit'));
                Route::get('sync_history', array('uses'=>'Admin\ChannelController@syncHistoryIndex', 'as'=>'byChannel.admin.channels.sync_history.index'));
            });


        });
    });

});
