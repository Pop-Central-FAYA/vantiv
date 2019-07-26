<?php
Route::get('health', 'Auth\AuthController@getLogin');

/**
 * Authentication
 */

Route::group(['namespace' => 'Auth'], function() {
    Route::get('login', 'AuthController@getLogin')->name('login');
    Route::post('login', 'AuthController@postLogin')->name('post.login');
    Route::get('/logout', 'AuthController@getLogout')->name('auth.logout');
    Route::get('/forget-password', 'AuthController@getForgetPassword')->name('password.forgot');
    Route::post('/forget-password/process', 'AuthController@processForgetPassword')->name('forget_password.process');
    Route::get('/proceed/password-change/{token}', 'AuthController@getChangePassword');
    Route::post('/change-password/process/{user_id}', 'AuthController@processChangePassword')->name('change_password.process');
    Route::get('register/verify/{token}', 'AuthController@verifyToken');
});

Route::get('user/complete-account/{id}', 'UserController@getCompleteAccount')->name('user.complete_registration')->middleware('signed');
Route::post('/user/complete-account/store/{id}', 'UserController@processCompleteAccount');

Route::group(['middleware' => 'auth'], function () {
    # We need a default index
    Route::get('/', ['as' => 'dashboard', 'uses' => 'Broadcaster\DashboardController@index']);
    Route::get('/user/profile', 'ProfileManagementsController@index')->name('user.profile')
                                ->middleware('permission:view.profile');
    Route::post('/profile/details/update', 'ProfileManagementsController@updateDetails')->name('profile.update.details')
                                        ->middleware('permission:update.profile');

    Route::group(['prefix' => 'user'], function () {
        Route::get('/all', 'UserController@index')->name('user.index')
            ->middleware('permission:view.user');
        Route::get('/invite', 'UserController@inviteUser')->name('user.invite')
            ->middleware('permission:create.user');
        Route::get('/edit/{id}', 'UserController@editUser')->name('user.edit')
            ->middleware('permission:update.user');
        Route::post('/update/{id}', 'UserController@updateUser');
        Route::post('/invite/store', 'UserController@processInvite');
        Route::get('/data-table', 'UserController@getDatatable');
        Route::post('/resend/invitation', 'UserController@resendInvitation')
            ->middleware('permission:update.user');
        Route::get('/status/update', 'UserController@updateStatus')
            ->middleware('permission:update.user');
    });

    Route::group(['namespace' => 'Broadcaster'], function() {
        /**
         * Media Program
         */
        Route::group(['prefix' => 'program-management'], function () {
            Route::get('/', 'ProgramManagementController@index')
                ->name('program.management.index')->middleware('permission:view.inventory');
            Route::get('/data-table', 'ProgramManagementController@formatToDataTable');
            Route::get('/edit/{id}', 'ProgramManagementController@edit')
                ->name('program.management.edit')->middleware('permission:update.inventory');
            Route::post('/update/{program_id}', 'ProgramManagementController@update')->name('program.management.update');
            Route::get('/details/{id}', 'ProgramManagementController@edit')->name('program.management.details');
            Route::get('/create', 'ProgramManagementController@create')
                ->name('program.management.create')->middleware('permission:create.inventory');
            Route::post('/store', 'ProgramManagementController@store')->name('program.management.store');
            Route::get('/get-rate-card/{station_id}', 'ProgramManagementController@fetRateCard');
        });
        /**
         * Rate Card Management
         */
        Route::group(['prefix' => 'rate-card-management'], function () {
            Route::get('/', 'RateCardManagementController@index')
                ->name('rate_card.management.index')->middleware('permission:view.rate_card');
            Route::get('/data-table', 'RateCardManagementController@formatToDatatable');
            Route::get('/create', 'RateCardManagementController@create')
                ->name('rate_card.management.create')->middleware('permission:create.rate_card');
            Route::post('/store', 'RateCardManagementController@store')->name('rate_card.management.store');
            Route::get('/edit/{rate_card_id}', 'RateCardManagementController@edit')
                ->name('rate_card.management.edit')->middleware('permission:update.rate_card');
            Route::post('/update/{rate_card_id}', 'RateCardManagementController@update')->name('rate_card.management.update');
            Route::get('/details/{rate_card_id}', 'RateCardManagementController@details')->name('rate_card.management.details');
        });
        /**
         * time belt management
         */
        Route::group(['prefix' => 'time-belt-management'], function () {
            Route::get('/', 'TimeBeltManagementController@index')
                ->name('time.belt.management.index')->middleware('permission:view.inventory');
            Route::get('/data-table', 'TimeBeltManagementController@formatToDatatable');
            Route::get('/details/{time_belt_id}', 'TimeBeltManagementController@details')->name('time.belt.management.details');
        });
        /**
         * Schedule Management System
         */
        Route::group(['prefix' => 'schedule'], function () {
            Route::get('/weekly', 'ScheduleController@getWeeklySchedule')->name('schedule.weekly')
                ->middleware('permission:view.schedule');
            Route::post('/weekly/navigate', 'ScheduleController@navigateWeeklySchedule');
        });
        /*
        * User Dashboard
        */
        Route::get('/broadcaster', 'DashboardController@index')->name('broadcaster.dashboard.index');
        Route::get('/campaign-management/dashboard', 'DashboardController@campaignManagementDashbaord')
            ->name('broadcaster.campaign_management')->middleware('permission:view.campaign|view.report');
        Route::get('/inventory-management/dashboard', 'DashboardController@inventoryManagementDashboard')
            ->name('broadcaster.inventory_management')->middleware('permission:view.inventory|view.report');
        Route::get('/inventory-management/reports', 'DashboardController@getFilteredInventoryReports')->name('broadcaster.inventory_management.timebelts_report');
        Route::get('/campaign-management/reports', 'DashboardController@getFilteredPublisherReports');
    });

    /*
    * Campaign
    */
    Route::group(['namespace' => 'Campaign', 'prefix' => 'campaign'], function () {
        Route::get('/campaigns-list', 'CampaignsController@campaignsList')
            ->name('campaign.list')->middleware('permission:view.campaign');
        Route::get('/active-campaigns', 'CampaignsController@allActiveCampaigns')->name('campaign.all');
        Route::get('/all-active-campaigns/data', 'CampaignsController@allActiveCampaignsData');
        Route::get('/all-campaigns/data', 'CampaignsController@filteredCampaignsData');
        Route::get('/campaign-general-information', 'CampaignsController@campaignGeneralInformation')
            ->name('campaign.get_campaign_general_information')->middleware('permission:create.campaign');
        Route::post('/campaign-general-information/store', 'CampaignsController@storeCampaignGeneralInformation')->name('campaign.store_campaign_general_information');
        Route::get('/advert-slot/result/{id}', 'CampaignsController@getAdSlotResult')->name('campaign.advert_slot');
        Route::get('/media-content/{id}', 'CampaignsController@getMediaContent')->name('campaign.get_media_content');
        Route::get('/media-content/store/{id}', 'CampaignsController@storeMediaContent')->name('campaign.store_media_content');
        Route::post('/remove-media-content/{client_id}/{upload_id}', 'CampaignsController@removeMediaContent')->name('uploads.remove');
        Route::get('/adslot-selection/pre-process/{id}', 'CampaignsController@preProcessAdslot')->name('campaign.adslot_preprocess');
        Route::get('/adslot-selection/{id}/{broadcaster}/{start_date}/{end_date}', 'CampaignsController@getAdslotSelection')->name('campaign.adslot_selection');
        Route::get('/preselected-adslot/store/{id}', 'CampaignsController@postPreselectedAdslot')->name('campaign.store_preselected_adslot');
        Route::get('/broadcaster-selection/{id}', 'CampaignsController@selectBroadcaster')->name('campaign.broadcaster_select');
        Route::get('/checkout/{id}', 'CampaignsController@checkout')->name('campaign.checkout');
        Route::get('/preselected-adslot/remove/{id}', 'CampaignsController@removePreselectedAdslot')->name('preselected_adslot.remove');
        Route::post('/campaign-hold/{id}', 'CampaignsController@postCampaign')->name('campaign.post_hold');
        Route::get('/campaign-on-hold/broadcaster/data', 'CampaignsController@getCampaignOnHold')->name('broadcaster.campaign.hold');
        Route::post('/submit-to-broadcasters/{campaign_id}', 'CampaignsController@submitWithOtherPaymentOption')->name('campaign.submit.other_payment');
        Route::post('/payment-process', 'CampaignsController@submitWithCardPaymentOption')->name('broadcaster.pay');
        Route::post('/update-campaign-budget', 'CampaignsController@updateBudget')->name('campaign_budget.update');
    });

    Route::group(['namespace' => 'Broadcaster', 'prefix' => 'campaign'], function() {
        Route::get('/cart/store', 'CampaignsController@postPreselectedAdslot')->name('broadcaster_campaign.cart');
        Route::post('/submit-campaign/{id}', 'CampaignsController@postCampaign')->name('submit.campaign');
        Route::get('/campaign-details/{id}', 'CampaignsController@campaignDetails')
            ->name('broadcaster.campaign.details')->middleware('permission:view.campaign');
        Route::get('/{user_id}', 'CampaignsController@filterByUser');
        Route::get('/media-channel/{campaign_id}', 'CampaignsController@getMediaChannel');
        Route::get('/compliance-graph/broadcaster', 'CampaignsController@complianceGraph');
        Route::get('/compliance-graph/filter/broadcaster', 'CampaignsController@complianceFilter')->name('broadcaster.campaign_details.compliance');
        Route::post('/update-campaign/information/{campaign_id}', 'CampaignsController@updateCampaignInformation')->name('broadcaster.campaign_information.update');
    });

    Route::get('/brand/get-industry', 'Campaign\CampaignsController@getBrandsIndustryAndSubIndustry');

    Route::get('file-update/{file_id}', 'MpoController@updateFiles')->name('file.change');

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
    * WalkIns Management
    */
    Route::group(['prefix' => 'walk-in'], function () {
        Route::get('/', 'WalkinsController@index')->name('walkins.all')
            ->middleware('permission:view.client');
        Route::post('/update/{client_id}', 'WalkinsController@updateWalKins')->name('walkins.update')
            ->middleware('permission:update.client');
        Route::post('/store', 'WalkinsController@store')->name('walkins.store')
            ->middleware('permission:create.client');
        Route::get('/delete/{id}', 'WalkinsController@delete')->name('walkins.delete');
        Route::get('/brand', 'WalkinsController@getSubIndustry');
        Route::get('/details/{client_id}', 'WalkinsController@getDetails')->name('walkins.details')
            ->middleware('permission:view.client');
    });

    Route::get('/presigned-url', 'S3Controller@getPresignedUrl');

    Route::get('/mpo/filter', 'MpoController@filterCompany');

    /*
    * Compliance summary
    */
    Route::get('/compliance/view-summary/{campaign_id}', 'Compliance\ComplianceController@downloadSummary')->name('compliance.download.summary');

    Route::post('sector/store', ['as' => 'sector.store', 'uses' => 'SectorController@store']);
    Route::delete('sector/{sector}', ['as' => 'sector.delete', 'uses' => 'SectorController@delete']);

    /**
     * Discounts
     */
    Route::group(['prefix' => 'discount'], function () {
        Route::get('/', 'DiscountController@index')->name('discount.index')
            ->middleware('permission:view.discount');
        Route::get('/data-table', 'DiscountController@dataTable');
        Route::get('/create', 'DiscountController@create')->name('discount.create')
            ->middleware('permission:create.discount');
        Route::get('/edit/{id}', 'DiscountController@edit')->name('discount.edit')
            ->middleware('permission:update.discount');
        Route::post('/store', 'DiscountController@store')->name('discount.store');
        Route::post('/update/{id}', 'DiscountController@update')->name('discount.update');
    });

    /**
     * MPOs
     */
    Route::group(['prefix' => 'mpos'], function () {
        Route::get('/pending_mpos_data', 'MpoController@pending_mpos_data');
        Route::get('all', 'MpoController@index')->name('all-mpos')->middleware('permission:view.mpo');
        Route::get('/all-data', 'MpoController@getAllData');
        Route::get('/pending/data', 'MpoController@pendingData');
        Route::get('pending', 'MpoController@pending_mpos')->name('pending-mpos');
        Route::get('/mpo-action/{mpo_id}', 'MpoController@mpoAction')->name('mpo.action');
        Route::get('/mpo-action/file-status/update/{file_code}/{campaign_id}/{mpo_id}', 'MpoController@update_file')
            ->name('files.update')->middleware('permission:update.mpo_status');
        Route::get('/rejected-files/{mpo_id}', 'MpoController@rejectedFiles')->name('mpo.rejected_files');
    });

    Route::get('/brand/get-industry', 'Campaign\CampaignsController@getBrandsIndustryAndSubIndustry');

    Route::get('file-update/{file_id}', 'MpoController@updateFiles')->name('file.change');

    Route::get('/presigned-url', 'S3Controller@getPresignedUrl');

    Route::get('/mpo/filter', 'MpoController@filterCompany');

    /*
        * Compliance summary
        */
    Route::get('/compliance/view-summary/{campaign_id}', 'Compliance\ComplianceController@downloadSummary')->name('compliance.download.summary');

    //route to filter client details by a company owner with multiple publishers
    Route::get('/client-details/{client_id}', 'WalkinsController@filterByPublisher');

    //get all brands per walkins or clients
    Route::get('/client/get-brands/{id}', 'BrandsController@getBrandsWithClients');

});

// Route::group(['domain' => env('SITE_URL', 'local.torch.docker.localhost')], $sspRoutes);
