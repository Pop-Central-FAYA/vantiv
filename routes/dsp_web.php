<?php
    Route::group(['namespace' => 'Dsp'], function () {
      Route::get('health', 'AuthController@getLogin');
    
        /**
         * Authentication
         */
        Route::get('login', 'AuthController@getLogin')->name('login');
        Route::post('login', 'AuthController@postLogin')->name('post.login');
        Route::get('logout', 'AuthController@getLogout')->name('auth.logout');
    

        Route::get('/forget-password', 'AuthController@getForgetPassword')->name('password.forgot');
        Route::post('/forget-password/process', 'AuthController@processForgetPassword')->name('forget_password.process');
        Route::get('/proceed/password-change/{token}', 'AuthController@getChangePassword');
        Route::post('/change-password/process/{user_id}', 'AuthController@processChangePassword')->name('change_password.process');
        

    
        Route::get('user/complete-account/{id}', 'UserController@getCompleteAccount')->name('user.complete_registration')->middleware('signed');
        Route::post('/user/complete-account/store/{id}', 'UserController@processCompleteAccount');

    });

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', ['as' => 'dashboard', 'uses' => 'DashboardController@index']);
        Route::get('agency/dashboard/campaigns', 'DashboardController@dashboardCampaigns');

        Route::group(['namespace' => 'Dsp', 'prefix' => 'campaigns'], function () {
            Route::get('/{status?}', 'CampaignsController@index')->name('agency.campaign.all')->middleware('permission:view.campaign');
            Route::get('/details/{id}', 'CampaignsController@getDetails')->name('agency.campaign.details');
            Route::get('/mpo-details/{id}', 'CampaignsController@mpoDetails')->name('agency.mpo.details');
            Route::get('/mpo/details/{campaign_mpo_id}', 'CampaignsController@campaignMpoDetails');
            Route::get('/mpo/export/{campaign_mpo_id}', 'CampaignsController@exportMpoAsExcel');
            Route::post('/mpo/associate-assets', 'CampaignsController@associateAssetsToMpo');
            Route::post('/mpo/details/{campaign_mpo_id}/adslots/delete', 'CampaignsController@deleteMultipleAdslots');
            Route::post('/mpo/details/{campaign_mpo_id}/adslots/update', 'CampaignsController@updateAdslots');
            Route::post('/mpo/details/{campaign_mpo_id}/adslots/store', 'CampaignsController@storeAdslots');
        });

        /*
         * User Management
         */

        Route::get('/user/manage', 'Dsp\UserManagementController@index')->name('agency.user_management');

        /**
         * Clients
         */
        Route::group(['prefix' => 'clients'], function () {
            Route::get('/list', 'ClientsController@clients')->name('clients.list')->middleware('permission:view.client');
            Route::get('/client/{client_id}', 'ClientsController@clientShow')->name('client.show')->middleware('permission:view.client');
            Route::get('/client/brand/{id}', 'ClientsController@getClientBrands')->name('client_brands');
            Route::get('/client/{client_id}/{user_id}', 'ClientsController@getCampaignData');
            Route::get('/client-month/{client_id}', 'ClientsController@filterByDate')->name('client.date');
            Route::get('/client-yearly/{client_id}', 'ClientsController@filterByYear')->name('client.year');
            Route::get('/client-brand/{id}/{client_id}', 'ClientsController@brandCampaign')->name('campaign.brand.client');
            Route::post('/update-client/{client_id}', 'ClientsController@updateClients')->name('agency.client.update')->middleware('permission:update.client');

            # This is not being used
            Route::post('/store', 'ClientsController@storeClients')->name('client.store')->middleware('permission:create.client');
        });

        Route::group(['prefix' => 'invoices'], function () {
            Route::get('/all', 'InvoiceController@all')->name('invoices.all')->middleware('permission:view.invoice');
            Route::get('/data', 'InvoiceController@getInvoiceDate');
            Route::get('/pending/data', 'InvoiceController@pendingData');
            Route::get('/pending', 'InvoiceController@pending')->name('invoices.pending');
            Route::post('/{invoice_id}/update', 'InvoiceController@approveInvoice')->name('invoices.update')->middleware('permission:view.invoice');
            Route::get('/details/{id}', 'InvoiceController@getInvoiceDetails')->name('invoice.details');
            Route::get('/export/pdf/{id}', 'InvoiceController@exportToPDF')->name('invoice.export');
        });

        Route::group(['namespace' => 'Dsp', 'prefix' => 'wallets'], function () {
            Route::get('/wallet/credit', 'WalletsController@create')->name('agency_wallet.create')->middleware('permission:create.wallet');
            Route::get('/wallet-statement', 'WalletsController@index')->name('agency_wallet.statement')->middleware('permission:view.wallet');
            Route::post('/wallet/amount', 'WalletsController@getAmount')->name('wallet.amount');
            Route::get('/wallet/amount/pay', 'WalletsController@getPay')->name('amount.pay');
            Route::post('/pay', 'WalletsController@pay')->name('pay');
            Route::get('/get-wallet/data', 'WalletsController@getData');
        });

        Route::group(['namespace' => 'Dsp', 'prefix' => 'reports'], function () {
            Route::get('/', 'ReportsController@index')->name('reports.index');
            Route::get('/campaign/all-data', 'ReportsController@getCampaign');
            Route::get('/revenue/all-data', 'ReportsController@getRevenue');
            //   Route::get('/client-filter/campaign', 'Agency\ReportsController@filterCampaignClient')->name('filter.client');
        });

        /**
         * Sectors
         */
        Route::group(['namespace' => 'Dsp\MediaPlan', 'prefix' => 'media-plan'], function () {
            Route::get('all/{status?}', 'MediaPlanController@index')->name('agency.media_plans');
            Route::get('/set-criterias', 'MediaPlanController@criteriaForm')->name('agency.media_plan.criteria_form')->middleware('permission:create.media_plan');
            Route::post('/create-plan', 'MediaPlanController@generateRatingsPost')->name('agency.media_plan.submit.criterias');
            Route::get('/summary/{id}', 'MediaPlanController@summary')->name('agency.media_plan.summary');
            Route::get('/approve/{id}', 'MediaPlanController@approvePlan')->name('agency.media_plan.approve')->middleware('permission:create.media_plan');
            Route::get('/decline/{id}', 'MediaPlanController@declinePlan')->name('agency.media_plan.decline')->middleware('permission:create.media_plan');
            Route::post('/get_approval/', 'MediaPlanController@postRequestApproval')->name('agency.media_plan.get_approval')->middleware('permission:create.media_plan');
            Route::get('/customise/{id}', 'MediaPlanController@getSuggestPlanById')->name('agency.media_plan.customize');
            Route::post('/customise-filter', 'MediaPlanController@setPlanSuggestionFilters')->name('agency.media_plan.customize-filter');
            Route::post('/select_plan', 'MediaPlanController@SelectPlanPost')->name('agency.media_plan.select_suggestions');
            Route::get('/createplan/{id}', 'MediaPlanController@createPlan')->name('agency.media_plan.create');
            Route::post('/finish_plan', 'MediaPlanController@completePlan')->name('agency.media_plan.submit.finish_plan');
            Route::get('/export/{id}', 'MediaPlanController@exportPlan')->name('agency.media_plan.export');
            Route::post('/store-programs', 'MediaPlanController@storePrograms')->name('media_plan.program.store');
            Route::post('/store-volume-discount', 'MediaPlanController@storeVolumeDiscount')->name('media_plan.volume_discount.store');
            Route::get('/convert-to-campaign/{id}', 'MediaPlanController@convertPlanToCampaign')->name('media_plan.campaign.create');
        });

        /**
         * File Position
         */
        Route::group(['prefix' => 'media-assets'], function () {
            Route::get('/', 'MediaAssetsController@index')->name('agency.media_assets')->middleware('permission:view.asset');
            Route::post('/create', 'MediaAssetsController@createAsset')->middleware('permission:create.asset');
            Route::post('/presigned-url', 'S3Controller@getPresignedUrl')->middleware('permission:create.asset');
            Route::get('/all', 'MediaAssetsController@getAssets')->middleware('permission:view.asset');
            Route::get('/delete/{id}', 'MediaAssetsController@deleteAsset')->middleware('permission:delete.asset');
            Route::get('/client/get-brands/{id}', 'BrandsController@getBrandsWithClients');
        });
        Route::get('/client/get-brands/{id}', 'BrandsController@getBrandsWithClients');
        Route::get('file-update/{file_id}', 'MpoController@updateFiles')->name('file.change');
          /*
         * Compliance summary
         */
        Route::get('/compliance/view-summary/{campaign_id}', 'Compliance\ComplianceController@downloadSummary')->name('compliance.download.summary');

        /**
         * User Management
         */

        Route::get('user/profile', [
            'as' => 'user.profile',
            'uses' => 'ProfileManagementsController@index',
            'middleware' => 'permission:view.profile',
        ]);

        Route::post('profile/details/update', [
            'as' => 'profile.update.details',
            'uses' => 'ProfileManagementsController@updateDetails',
            'middleware' => 'permission:update.profile',
        ]);

        Route::group(['namespace' => 'Dsp', 'prefix' => 'user'], function () {
            Route::get('/all', 'UserController@index')->name('agency.user.index')->middleware('permission:view.user');
            Route::get('/invite', 'UserController@inviteUser')->name('agency.user.invite')->middleware('permission:create.user');
            Route::get('/edit/{id}', 'UserController@editUser')->name('agency.user.edit')->middleware('permission:update.user');
            Route::post('/update/{id}', 'UserController@updateUser');
            Route::post('/invite/store', 'UserController@processInvite');
            Route::get('/data-table', 'UserController@getDatatable');
            Route::post('/resend/invitation', 'UserController@resendInvitation')->middleware('permission:update.user');
            Route::get('/status/update', 'UserController@updateStatus')->middleware('permission:update.user');
        });
 /*
         * WalkIns Management
         */

        Route::group(['prefix' => 'walk-in'], function () {
            Route::post('/update/{client_id}', 'WalkinsController@updateWalKins')->name('walkins.update')
                ->middleware('permission:update.client');
            Route::post('/store', 'WalkinsController@store')->name('walkins.store')
                ->middleware('permission:create.client');
            Route::get('/delete/{id}', 'WalkinsController@delete')->name('walkins.delete');
            Route::get('/brand', 'WalkinsController@getSubIndustry');
            Route::get('/details/{client_id}', 'WalkinsController@getDetails')->name('walkins.details')
                ->middleware('permission:view.client');
        });

        Route::group(['prefix' => 'brands'], function () {
            Route::get('/', 'BrandsController@index')->name('brand.all');
            Route::get('/create', 'BrandsController@create')->name('brand.create');
            Route::post('/create/store', 'BrandsController@store')->name('brand.store');
            Route::post('/brands/update/{id}', 'BrandsController@update')->name('brands.update');
            Route::get('/brands/delete/{id}', 'BrandsController@delete')->name('brands.delete');
            Route::get('/search-brands', 'BrandsController@search')->name('broadcasters.brands.search');
            Route::get('/details/{id}/{client_id}', 'BrandsController@getBrandDetails')->name('brand.details');
        });
        Route::get('/check-brand-existence', 'BrandsController@checkBrandExistsWithSameInformation');
        Route::get('/presigned-url', 'S3Controller@getPresignedUrl');
         /*
         * new Client route
         */
        Route::post('/clients', 'ClientController@storeClient')->name('client.create');
       

         /**
         * Company Management
         */

        Route::group(['namespace' => 'Dsp'], function () {
            Route::patch('/company/{id}', 'CompanyController@update')->name('company.update');
        });
        
        Route::group(['namespace' => 'Dsp'], function() {
            //@todo possibly create another name for the index route
            Route::get('/ad-vendors/index', 'AdVendorController@index')->name('ad-vendor.index');
            //@todo possibly wrap the api endpoints with a versioned api
            Route::get('/ad-vendors', 'AdVendorController@list')->name('ad-vendor.list');
            Route::get('/ad-vendors/{id}', 'AdVendorController@get')->name('ad-vendor.get');
            Route::post('/ad-vendors', 'AdVendorController@create')->name('ad-vendor.create');
            Route::patch('/ad-vendors/{id}', 'AdVendorController@update')->name('ad-vendor.update');
        });

          /*
         * new Brand route
         */
        Route::group(['namespace' => 'Dsp'], function () {
            Route::post('/brands', 'BrandController@create')->name('brand.create');
            Route::patch('/brands/{id}', 'BrandController@update')->name('brand.update');
            Route::delete('/brands/{id}', 'BrandController@destroy')->name('brand.destroy');
        });

    });

 