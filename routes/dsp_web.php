<?php
    /**
     * Public accessed share link
     */
    Route::group(['prefix' => 'guest', 'namespace' => 'Guest'], function() {
        Route::get('/mpos/index/{id}', 'MpoController@index')->name('guest.mpo_share_link');
        Route::get('/mpos/{id}/temporary-url', 'MpoController@getTemporaryUrl')->name('public.mpo.export.temporary_url');
        Route::get('/mpos/{id}/export', 'MpoController@export')->name('public.mpo.export');
        Route::get('/{code}', 'MpoController@redirectToIndex')->name('guest.mpos.short_links');

        Route::group(['prefix' => 'api'], function() {
            Route::post('/mpos/{mpo_id}', 'MpoController@acceptMpo')->name('mpos.accept');
        });
    });

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

        Route::get('/', 'Dsp\DashboardController@index')->name('dashboard');

        Route::group(['namespace' => 'Dsp', 'prefix' => 'campaigns'], function () {
            Route::get('/{status?}', 'CampaignsController@index')->name('agency.campaign.all');
            Route::get('/details/{id}/{group?}', 'CampaignsController@getDetails')->name('agency.campaign.details');

            #api
            Route::group(['prefix' => 'api'], function() {
                Route::get('/{campaign_id}/vendors/{ad_vendor_id}/mpos', 'MpoController@vendorMpoList')->name('campaign.vendors.mpos.lists');
                Route::post('/{campaign_id}/assign-follower', 'CampaignsController@assignFollower')->name('campaign.assign_follower');
            });
            Route::get('/{campaign_id}/groups/{group_param}', 'CampaignsController@groupCampaignTimeBelts');
            Route::post('/{campaign_id}/adslots', 'CampaignsController@storeAdslot')->name('campaigns.adslot.store');
            Route::patch('/{campaign_id}', 'CampaignsController@updateMultipleAdslots')->name('campaigns.adslots.update');
            Route::patch('/{campaign_id}/adslots/{adslot_id}', 'CampaignsController@updateAdslot')->name('update.campaign_mpo');
            Route::delete('/{campaign_id}/adslots/{adslot_id}', 'CampaignsController@deleteAdslot');
            Route::post('/{campaign_id}/associate-assets', 'CampaignsController@associateAssetsToAdslot')->name('campaigns.assets.associate');

            Route::get('/{campaign_id}/mpos', 'MpoController@list')->name('mpos.list');
            Route::post('/{campaign_id}/mpos', 'MpoController@generateMpo')->name('mpos.store');
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
            /*** New Routes ****/
            Route::post('/{id}/suggestions', 'MediaPlanController@storeSuggestions')->name('agency.media_plan.select_suggestions');

            /*** Old Routes ***/
            Route::get('all/{status?}', 'MediaPlanController@index')->name('agency.media_plans');
            Route::get('/set-criterias', 'MediaPlanController@criteriaForm')->name('agency.media_plan.criteria_form')->middleware('permission:create.media_plan');
            // Route::post('/create-plan', 'MediaPlanController@generateRatingsPost')->name('agency.media_plan.submit.criterias');
            Route::post('/create-plan', 'MediaPlanController@createNewMediaPlan')->name('agency.media_plan.submit.criterias');
            Route::get('/summary/{id}', 'MediaPlanController@summary')->name('agency.media_plan.summary');
            Route::post('/change-status', 'MediaPlanController@changeMediaPlanStatus')->name('agency.media_plan.change_status')->middleware('permission:create.media_plan');
            Route::post('/get_approval/', 'MediaPlanController@postRequestApproval')->name('agency.media_plan.get_approval')->middleware('permission:create.media_plan');
            Route::get('/customise/{id}', 'MediaPlanController@stationDetails')->name('agency.media_plan.customize');

            Route::post('/customise-filter', 'MediaPlanController@setPlanSuggestionFilters')->name('agency.media_plan.customize-filter');
            // Route::post('/select_plan', 'MediaPlanController@SelectPlanPost')->name('agency.media_plan.select_suggestions');
            Route::get('/createplan/{id}', 'MediaPlanController@createPlan')->name('agency.media_plan.create');
            Route::post('/finish_plan', 'MediaPlanController@completePlan')->name('agency.media_plan.submit.finish_plan');
            Route::get('/export/{id}', 'MediaPlanController@exportPlan')->name('agency.media_plan.export');
            Route::post('/store-programs', 'MediaPlanController@storePrograms')->name('media_plan.program.store');
            Route::post('/store-volume-discount', 'MediaPlanController@storeVolumeDiscount')->name('media_plan.volume_discount.store');
            Route::get('/convert-to-campaign/{id}', 'MediaPlanController@convertPlanToCampaign')->name('media_plan.campaign.create');

            Route::group(['prefix' => 'api'], function() {
                Route::post('/{id}/comments', 'MediaPlanCommentController@createComment')->name('agency.media_plan.comment.store');
                Route::get('/{id}/comments', 'MediaPlanCommentController@getComments')->name('agency.media_plan.comment.all');
                
                Route::post('/{id}/clone', 'MediaPlanController@clonePlan')->name('media_plan.clone');
                
                Route::delete('/{id}/delete', 'MediaPlanController@deletePlan')->name('media_plan.delete');
            });
        });

        /**
         * File Position
         */
        Route::group(['prefix' => 'media-assets'], function () {
            Route::get('/', 'MediaAssetsController@index')->name('agency.media_assets')->middleware('permission:view.asset');
            Route::post('/create', 'MediaAssetsController@createAsset')->middleware('permission:create.asset');
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
        /*
        * new Client route
        */
        Route::group(['namespace' => 'Dsp'], function () {
            Route::post('/clients', 'ClientController@create')->name('client.create');
            Route::patch('/clients/{id}', 'ClientController@update')->name('client.update');
            Route::get('/clients', 'ClientController@list')->name('client.list');
            Route::get('/clients/index', 'ClientController@index')->name('client.index');
            Route::get('/clients/{id}/details', 'ClientController@details')->name('client.details');
            Route::get('/clients/{id}', 'ClientController@get')->name('client.get');
         });

        /**
        * Company Management
        */

        Route::group(['namespace' => 'Dsp'], function () {
            Route::get('/company/index', 'CompanyController@index')->name('company.index');
            Route::patch('/company/{id}', 'CompanyController@update')->name('company.update');
        });

        Route::group(['namespace' => 'Dsp'], function() {
            Route::get('/ad-vendors', 'AdVendorController@index')->name('ad-vendor.index');
            Route::get('/ad-vendors/{id}/details', 'AdVendorController@details')->name('ad-vendor.details');

            Route::group(['prefix' => 'api'], function () {
                Route::get('/ad-vendors', 'AdVendorController@list')->name('ad-vendor.list');
                Route::get('/ad-vendors/{id}', 'AdVendorController@get')->name('ad-vendor.get');
                Route::post('/ad-vendors', 'AdVendorController@create')->name('ad-vendor.create');
                Route::patch('/ad-vendors/{id}', 'AdVendorController@update')->name('ad-vendor.update');
                Route::get('/ad-vendors/{id}/details', 'AdVendorController@getDetails')->name('ad-vendor.get_details');
            });
        });

        Route::group(['namespace' => 'Dsp'], function() {
            Route::get('/mpos/{mpo_id}', 'MpoController@details')->name('mpos.details');
            Route::get('/mpos/{mpo_id}/export', 'MpoController@exportMpoAsExcel')->name('mpos.export');

            #api
            Route::group(['prefix' => 'api'], function() {
                Route::get('/mpos/{id}/share-links', 'MpoController@getActiveLink');
                Route::post('/mpos/{id}/share-links', 'MpoController@storeLink')->name('mpo_share_link.store');
                Route::post('/mpos/{id}/submit', 'MpoController@submitToVendor')->name('mpo_share_link.submit');
            });
        });
          /*
         * new Brand route
         */
        Route::group(['namespace' => 'Dsp'], function () {
            Route::post('/brands', 'BrandController@create')->name('brand.create');
            Route::patch('/brands/{id}', 'BrandController@update')->name('brand.update');
        });

        /**
         * Generation of REACH routes
         */
        Route::group(['namespace' => 'Dsp'], function() {
            Route::group(['prefix' => 'api'], function() {
                /**
                 * @todo, after merging this branch with Kunle's breaking changes, switch the name of the route
                 */
                Route::get('/reach/{plan_id}', 'ReachController@getReach')->name('reach.get');
                Route::get('/reach/{plan_id}/timebelts', 'ReachController@getStationTimebeltReach')->name('reach.get-timebelts');
            });
        });

        /*
        * new user management route
        */
        Route::group(['namespace' => 'Dsp'], function () {
            Route::get('/users', 'UserController@index')->name('users.index');

            Route::group(['prefix' => 'api'], function () {
                Route::get('/users', 'UserController@list')->name('users.list');
                Route::post('/users', 'UserController@create')->name('users.invite');
                Route::patch('/users/{id}', 'UserController@update')->name('users.update');
                Route::post('/users/{id}/resend-invitaion', 'UserController@resend')->name('users.reinvite');
                Route::delete('/users/{id}', 'UserController@delete')->name('users.delete');
            });
         });

          /*
        * new user management route 
        */
        Route::group(['namespace' => 'Dsp'], function () {
            Route::get('/profile', 'ProfileController@index')->name('profile.index');

            Route::group(['prefix' => 'api'], function () {
                Route::get('/profile', 'ProfileController@get')->name('profile.get');
                Route::patch('/profile/{id}', 'ProfileController@update')->name('profile.update');
                Route::post('/profile/password', 'ProfileController@updatePassword')->name('password.update');
             });
         });


     Route::group(['namespace' => 'Dsp'], function () {
        Route::get('/password/{token}', 'ProfileController@resetPassword')->name('password.reset');
        Route::group(['prefix' => 'api'], function () {
            Route::post('/password', 'ProfileController@processResetPassword')->name('process.password.reset');
         });
     });

    Route::post('/presigned-url', 'S3Controller@getPresignedUrl')->name('presigned.url');
});
