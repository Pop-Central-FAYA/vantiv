<?php

$sspRoutes = function () {

    /**
     * Authentication
     */
    Route::get('login', 'Auth\AuthController@getLogin')->name('login');
    Route::post('login', 'Auth\AuthController@postLogin')->name('post.login');

    Route::get('/auth-broadcaster/signup', 'BroadcasterAuthController@getRegister')->name('broadcaster.register.form');
    Route::post('/auth-broadcaster/signup/process', 'BroadcasterAuthController@postRegister')->name('broadcaster.signup');

    Route::get('/auth-agent/signup', 'Dsp\AgencyAuthController@getRegister')->name('agency.register.form');
    Route::post('/auth-agent/signup/process', 'Dsp\AgencyAuthController@postRegister')->name('agency.signup');

    Route::get('/auth-advertiser/signup', 'Advertiser\AdvertiserAuthController@getRegister')->name('advertiser.register.form');
    Route::post('/auth-advertiser/signup/process', 'Advertiser\AdvertiserAuthController@postRegister')->name('advertiser.signup');

    Route::get('logout', [
        'as' => 'auth.logout',
        'uses' => 'Auth\AuthController@getLogout',
    ]);

    Route::get('/forget-password', 'Auth\AuthController@getForgetPassword')->name('password.forgot');
    Route::post('/forget-password/process', 'Auth\AuthController@processForgetPassword')->name('forget_password.process');
    Route::get('/proceed/password-change/{token}', 'Auth\AuthController@getChangePassword');
    Route::post('/change-password/process/{user_id}', 'Auth\AuthController@processChangePassword')->name('change_password.process');

    Route::get('/cron-job/compliance-report', 'CronjobController@getCompliance');

    Route::get('/cron-job/validate-campaign', 'CronjobController@validateCampaign');

    Route::get('register/verify/{token}', 'Auth\AuthController@verifyToken');

    Route::get('/reg-admin', 'AdminAuthController@getAdmin')->name('admin.register.get');

    Route::get('user/complete-account/{id}', 'UserController@getCompleteAccount')->name('user.complete_registration')->middleware('signed');
    Route::post('/user/complete-account/store/{id}', 'UserController@processCompleteAccount');

    Route::post('/admin/post', 'AdminAuthController@postRegister')->name('admin.post');

    Route::group(['middleware' => 'auth'], function () {

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

        Route::group(['prefix' => 'regions'], function () {
            Route::get('/', 'RegionsController@index')->name('admin.region.index');
            Route::get('/data', 'RegionsController@data');
            Route::get('/create', 'RegionsController@create')->name('admin.region.create');
            Route::post('/store', 'RegionsController@store')->name('admin.region.store');
            Route::post('/update/{id}', 'RegionsController@update')->name('admin.region.update');
            Route::get('/delete/{id}', 'RegionsController@delete')->name('admin.region.delete');
        });

        Route::group(['prefix' => 'target-audiences'], function () {
            Route::get('/', 'TargetAudienceController@index')->name('target_audience.index');
            Route::get('/data', 'TargetAudienceController@getData');
            Route::get('/create', 'TargetAudienceController@create')->name('admin.target_audience.create');
            Route::post('/store', 'TargetAudienceController@store')->name('admin.target_audience.store');
            Route::post('/update/{id}', 'TargetAudienceController@update')->name('admin.target_audience.update');
            Route::get('/delete/{id}', 'TargetAudienceController@delete')->name('admin.target_audience.delete');
        });

        Route::group(['prefix' => 'admin-broadcaster'], function () {
            Route::get('/', 'BroadcasterManagementController@index')->name('admin.broadcaster.index');
            Route::get('/broadcaster-data', 'BroadcasterManagementController@braodcasterData');
            Route::get('/details/{id}', 'BroadcasterManagementController@broadcasterDetails')->name('admin.broadcaster.details');
            Route::get('/inventory/{id}', 'BroadcasterManagementController@getInventory')->name('admin.broadcaster.upload_inventory');
            Route::post('/inventory/store/{id}', 'BroadcasterManagementController@storeInventory')->name('upload_inventory.store');
        });

        Route::group(['prefix' => 'day-parts'], function () {
            Route::get('/', 'AdminDayPartsController@index')->name('admin.dayparts');
            Route::get('/data', 'AdminDayPartsController@getData');
            Route::get('/create', 'AdminDayPartsController@create')->name('admin.daypart.create');
            Route::post('/store', 'AdminDayPartsController@store')->name('admin.daypart.store');
            Route::post('/update/{id}', 'AdminDayPartsController@update')->name('admin.daypart.update');
            Route::get('/delete/{id}', 'AdminDayPartsController@delete')->name('admin.daypart.delete');
        });

        /**
         * Media Inventory
         */
        Route::group(['prefix' => 'program-management'], function () {
            Route::get('/', 'Broadcaster\ProgramManagementController@index')
                ->name('program.management.index')->middleware('permission:view.inventory');
            Route::get('/data-table', 'Broadcaster\ProgramManagementController@formatToDataTable');
            Route::get('/edit/{id}', 'Broadcaster\ProgramManagementController@edit')
                ->name('program.management.edit')->middleware('permission:update.inventory');
            Route::post('/update/{program_id}', 'Broadcaster\ProgramManagementController@update')->name('program.management.update');
            Route::get('/details/{id}', 'Broadcaster\ProgramManagementController@edit')->name('program.management.details');
            Route::get('/create', 'Broadcaster\ProgramManagementController@create')
                ->name('program.management.create')->middleware('permission:create.inventory');
            Route::post('/store', 'Broadcaster\ProgramManagementController@store')->name('program.management.store');
            Route::get('/get-rate-card/{station_id}', 'Broadcaster\ProgramManagementController@fetRateCard');
        });

        /**
         * Rate Card Management
         */
        Route::group(['prefix' => 'rate-card-management'], function () {
            Route::get('/', 'Broadcaster\RateCardManagementController@index')
                ->name('rate_card.management.index')->middleware('permission:view.rate_card');
            Route::get('/data-table', 'Broadcaster\RateCardManagementController@formatToDatatable');
            Route::get('/create', 'Broadcaster\RateCardManagementController@create')
                ->name('rate_card.management.create')->middleware('permission:create.rate_card');
            Route::post('/store', 'Broadcaster\RateCardManagementController@store')->name('rate_card.management.store');
            Route::get('/edit/{rate_card_id}', 'Broadcaster\RateCardManagementController@edit')
                ->name('rate_card.management.edit')->middleware('permission:update.rate_card');
            Route::post('/update/{rate_card_id}', 'Broadcaster\RateCardManagementController@update')->name('rate_card.management.update');
            Route::get('/details/{rate_card_id}', 'Broadcaster\RateCardManagementController@details')->name('rate_card.management.details');
        });

        /**
         * time belt management
         */
        Route::group(['prefix' => 'time-belt-management'], function () {
            Route::get('/', 'Broadcaster\TimeBeltManagementController@index')
                ->name('time.belt.management.index')->middleware('permission:view.inventory');
            Route::get('/data-table', 'Broadcaster\TimeBeltManagementController@formatToDatatable');
            Route::get('/details/{time_belt_id}', 'Broadcaster\TimeBeltManagementController@details')->name('time.belt.management.details');
        });

        /**
         * Schedule Management System
         */
        Route::group(['prefix' => 'schedule'], function () {
            Route::get('/weekly', 'Broadcaster\ScheduleController@getWeeklySchedule')->name('schedule.weekly')
                ->middleware('permission:view.schedule');
        });

        /*
         * Campaign
         */
        Route::group(['prefix' => 'campaign'], function () {
            Route::get('/campaigns-list', 'Campaign\CampaignsController@campaignsList')
                ->name('campaign.list')->middleware('permission:view.campaign');
            Route::get('/active-campaigns', 'Campaign\CampaignsController@allActiveCampaigns')->name('campaign.all');
            Route::get('/all-active-campaigns/data', 'Campaign\CampaignsController@allActiveCampaignsData');
            Route::get('/all-campaigns/data', 'Campaign\CampaignsController@filteredCampaignsData');
            Route::get('/campaign-general-information', 'Campaign\CampaignsController@campaignGeneralInformation')
                ->name('campaign.get_campaign_general_information')->middleware('permission:create.campaign');
            Route::post('/campaign-general-information/store', 'Campaign\CampaignsController@storeCampaignGeneralInformation')->name('campaign.store_campaign_general_information');
            Route::get('/advert-slot/result/{id}', 'Campaign\CampaignsController@getAdSlotResult')->name('campaign.advert_slot');
            Route::get('/media-content/{id}', 'Campaign\CampaignsController@getMediaContent')->name('campaign.get_media_content');
            Route::get('/media-content/store/{id}', 'Campaign\CampaignsController@storeMediaContent')->name('campaign.store_media_content');
            Route::post('/remove-media-content/{client_id}/{upload_id}', 'Campaign\CampaignsController@removeMediaContent')->name('uploads.remove');
            Route::get('/adslot-selection/pre-process/{id}', 'Campaign\CampaignsController@preProcessAdslot')->name('campaign.adslot_preprocess');
            Route::get('/adslot-selection/{id}/{broadcaster}/{start_date}/{end_date}', 'Campaign\CampaignsController@getAdslotSelection')->name('campaign.adslot_selection');
            Route::get('/preselected-adslot/store/{id}', 'Campaign\CampaignsController@postPreselectedAdslot')->name('campaign.store_preselected_adslot');
            Route::get('/broadcaster-selection/{id}', 'Campaign\CampaignsController@selectBroadcaster')->name('campaign.broadcaster_select');
            Route::get('/checkout/{id}', 'Campaign\CampaignsController@checkout')->name('campaign.checkout');
            Route::get('/preselected-adslot/remove/{id}', 'Campaign\CampaignsController@removePreselectedAdslot')->name('preselected_adslot.remove');
            Route::post('/campaign-hold/{id}', 'Campaign\CampaignsController@postCampaign')->name('campaign.post_hold');
            Route::get('/campaign-on-hold/broadcaster/data', 'Campaign\CampaignsController@getCampaignOnHold')->name('broadcaster.campaign.hold');
            Route::get('/campaign-on-hold/agency/data', 'Campaign\CampaignsController@getCampaignOnHold')->name('agency.campaigns.hold');
            Route::post('/submit-to-broadcasters/{campaign_id}', 'Campaign\CampaignsController@submitWithOtherPaymentOption')->name('campaign.submit.other_payment');
            Route::post('/payment-process', 'Campaign\CampaignsController@submitWithCardPaymentOption')->name('broadcaster.pay');
            Route::post('/submit-to-broadcasters/agency/{campaign_id}', 'Campaign\CampaignsController@submitAgencyCampaign')->name('agency.campaign.update');
            Route::post('/update-campaign-budget', 'Campaign\CampaignsController@updateBudget')->name('campaign_budget.update');
            Route::get('/create/step3_1/{id}', 'Broadcaster\CampaignsController@storeStep3_1')->name('campaign.create3_1');
            Route::get('/create/step4/{id}/{broadcaster}/{start_date}/{end_date}', 'Broadcaster\CampaignsController@createStep4')->name('campaign.create4');
            Route::get('/cart/store', 'Broadcaster\CampaignsController@postPreselectedAdslot')->name('broadcaster_campaign.cart');

            Route::post('/submit-campaign/{id}', 'Broadcaster\CampaignsController@postCampaign')->name('submit.campaign');

            Route::get('/campaign-details/{id}', 'Broadcaster\CampaignsController@campaignDetails')
                ->name('broadcaster.campaign.details')->middleware('permission:view.campaign');

            Route::get('/{user_id}', 'Broadcaster\CampaignsController@filterByUser');

            Route::get('/media-channel/{campaign_id}', 'Broadcaster\CampaignsController@getMediaChannel');

            Route::get('/compliance-graph/broadcaster', 'Broadcaster\CampaignsController@complianceGraph');

            Route::get('/compliance-graph/filter/broadcaster', 'Broadcaster\CampaignsController@complianceFilter')->name('broadcaster.campaign_details.compliance');

            Route::post('/update-campaign/information/{campaign_id}', 'Broadcaster\CampaignsController@updateCampaignInformation')->name('broadcaster.campaign_information.update');
        });

        Route::get('/brand/get-industry', 'Campaign\CampaignsController@getBrandsIndustryAndSubIndustry');

        Route::get('file-update/{file_id}', 'MpoController@updateFiles')->name('file.change');

        /*
         * Broadcaster User Management
         */
        Route::group(['prefix' => 'broadcaster'], function () {
            Route::get('/users', 'BroadcasterAuthController@allUser')->name('broadcaster.user.all');
            Route::get('/user-data', 'BroadcasterAuthController@userData');
            Route::get('/users/create', 'BroadcasterAuthController@createUser')->name('broadcaster.user.create');
            Route::post('/user/create/store', 'BroadcasterAuthController@postBroadcasterUser')->name('broadcaster.post.user');
            Route::get('/user/delete/{id}', 'BroadcasterAuthController@deleteBroadcasterUser')->name('broadcaster_user.delete');
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

        /**
         * Sectors
         */

        Route::get('sectors', ['as' => 'sector.index', 'uses' => 'SectorController@index']);

        Route::get('sector/create', ['as' => 'sector.create', 'uses' => 'SectorController@create',
        ]);

        Route::post('sector/store', ['as' => 'sector.store', 'uses' => 'SectorController@store']);

        Route::delete('sector/{sector}', ['as' => 'sector.delete', 'uses' => 'SectorController@delete']);

        /**
         * DayParts
         */

        Route::resource('dayparts', 'DayPartController');

        /*
         * User Dashboard
         */

        //Route::get('/', ['as' => 'dashboard', 'uses' => 'DashboardController@index']);

        Route::get('/broadcaster', 'Broadcaster\DashboardController@index')->name('broadcaster.dashboard.index');
        Route::get('/campaign-management/dashboard', 'Broadcaster\DashboardController@campaignManagementDashbaord')
            ->name('broadcaster.campaign_management')->middleware('permission:view.campaign|view.report');
        Route::get('/inventory-management/dashboard', 'Broadcaster\DashboardController@inventoryManagementDashboard')
            ->name('broadcaster.inventory_management')->middleware('permission:view.inventory|view.report');
        Route::get('/inventory-management/reports', 'Broadcaster\DashboardController@getFilteredInventoryReports')->name('broadcaster.inventory_management.timebelts_report');
        Route::get('/campaign-management/reports', 'Broadcaster\DashboardController@getFilteredPublisherReports');
        Route::get('agency/dashboard/campaigns', 'DashboardController@dashboardCampaigns');
        Route::get('agency/dashboard/media-plans', 'DashboardController@dashboardMediaPlans');

        /**
         * Adslot
         */
        Route::group(['prefix' => '/adslot'], function () {
            Route::get('/', 'AdslotController@index')->name('adslot.all');
            Route::get('/data', 'AdslotController@adslotData');
            Route::get('/create', 'AdslotController@create')->name('adslot.create');
            Route::post('/store', 'AdslotController@store')->name('adslot.store');
            Route::post('/update/{adslot}', 'AdslotController@update')->name('adslot.update');
            Route::get('/{region_id}', 'AdslotController@getAdslotByRegion')->name('adslot.region');
        });

        /**
         * File Position
         */
        Route::group(['prefix' => 'positions'], function () {
            Route::get('/position-create', 'PositionController@createPosition')->name('position.create');
            Route::post('/position-store', 'PositionController@storePosition')->name('position.store');
            Route::get('/edit-position/{id}', 'PositionController@editPosition')->name('position.edit');
            Route::post('/update-position/{id}', 'PositionController@updatePosition')->name('position.update');
            Route::get('/position-delete/{id}', 'PositionController@deletePosition')->name('position.delete');
        });

        /**
         * Hourly Ranges
         */

        Route::group(['prefix' => 'hourly-ranges'], function () {
            Route::get('/', 'HourlyController@index')->name('hourly.all');
        });

        Route::group(['prefix' => 'time'], function () {
            Route::get('/', 'SecondsController@index')->name('seconds.all');
        });

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

        Route::group(['prefix' => 'reports'], function () {
            Route::get('/', 'ReportController@index')->name('reports');
            Route::get('total-volume-campaigns/all-data', 'ReportController@HVCdata');
            Route::get('paid-invoices/all-data', 'ReportController@PIdata');
            Route::get('/periodic-sales/all', 'ReportController@psData');
            Route::get('/total-volume-of-campaign/all', 'ReportController@tvcData');
            Route::get('/high-day-parts/all', 'ReportController@hpdData');
            Route::get('/high-days/all', 'ReportController@hpdaysData');
        });

        Route::group(['prefix' => 'regions'], function () {
            Route::get('/', 'RegionsController@index')->name('admin.region.index');
            Route::get('/data', 'RegionsController@data');
            Route::get('/create', 'RegionsController@create')->name('admin.region.create');
            Route::post('/store', 'RegionsController@store')->name('admin.region.store');
            Route::post('/update/{id}', 'RegionsController@update')->name('admin.region.update');
            Route::get('/delete/{id}', 'RegionsController@delete')->name('admin.region.delete');
        });

        Route::group(['prefix' => 'target-audiences'], function () {
            Route::get('/', 'TargetAudienceController@index')->name('target_audience.index');
            Route::get('/data', 'TargetAudienceController@getData');
            Route::get('/create', 'TargetAudienceController@create')->name('admin.target_audience.create');
            Route::post('/store', 'TargetAudienceController@store')->name('admin.target_audience.store');
            Route::post('/update/{id}', 'TargetAudienceController@update')->name('admin.target_audience.update');
            Route::get('/delete/{id}', 'TargetAudienceController@delete')->name('admin.target_audience.delete');
        });

        Route::group(['prefix' => 'admin-broadcaster'], function () {
            Route::get('/', 'BroadcasterManagementController@index')->name('admin.broadcaster.index');
            Route::get('/broadcaster-data', 'BroadcasterManagementController@braodcasterData');
            Route::get('/details/{id}', 'BroadcasterManagementController@broadcasterDetails')->name('admin.broadcaster.details');
            Route::get('/inventory/{id}', 'BroadcasterManagementController@getInventory')->name('admin.broadcaster.upload_inventory');
            Route::post('/inventory/store/{id}', 'BroadcasterManagementController@storeInventory')->name('upload_inventory.store');
        });

        Route::group(['prefix' => 'day-parts'], function () {
            Route::get('/', 'AdminDayPartsController@index')->name('admin.dayparts');
            Route::get('/data', 'AdminDayPartsController@getData');
            Route::get('/create', 'AdminDayPartsController@create')->name('admin.daypart.create');
            Route::post('/store', 'AdminDayPartsController@store')->name('admin.daypart.store');
            Route::post('/update/{id}', 'AdminDayPartsController@update')->name('admin.daypart.update');
            Route::get('/delete/{id}', 'AdminDayPartsController@delete')->name('admin.daypart.delete');
        });

        /**
         * Media Inventory
         */
        Route::group(['prefix' => 'program-management'], function () {
            Route::get('/', 'Broadcaster\ProgramManagementController@index')
                ->name('program.management.index')->middleware('permission:view.inventory');
            Route::get('/data-table', 'Broadcaster\ProgramManagementController@formatToDataTable');
            Route::get('/edit/{id}', 'Broadcaster\ProgramManagementController@edit')
                ->name('program.management.edit')->middleware('permission:update.inventory');
            Route::post('/update/{program_id}', 'Broadcaster\ProgramManagementController@update')->name('program.management.update');
            Route::get('/details/{id}', 'Broadcaster\ProgramManagementController@edit')->name('program.management.details');
            Route::get('/create', 'Broadcaster\ProgramManagementController@create')
                ->name('program.management.create')->middleware('permission:create.inventory');
            Route::post('/store', 'Broadcaster\ProgramManagementController@store')->name('program.management.store');
            Route::get('/get-rate-card/{station_id}', 'Broadcaster\ProgramManagementController@fetRateCard');
        });

        /**
         * Rate Card Management
         */
        Route::group(['prefix' => 'rate-card-management'], function () {
            Route::get('/', 'Broadcaster\RateCardManagementController@index')
                ->name('rate_card.management.index')->middleware('permission:view.rate_card');
            Route::get('/data-table', 'Broadcaster\RateCardManagementController@formatToDatatable');
            Route::get('/create', 'Broadcaster\RateCardManagementController@create')
                ->name('rate_card.management.create')->middleware('permission:create.rate_card');
            Route::post('/store', 'Broadcaster\RateCardManagementController@store')->name('rate_card.management.store');
            Route::get('/edit/{rate_card_id}', 'Broadcaster\RateCardManagementController@edit')
                ->name('rate_card.management.edit')->middleware('permission:update.rate_card');
            Route::post('/update/{rate_card_id}', 'Broadcaster\RateCardManagementController@update')->name('rate_card.management.update');
            Route::get('/details/{rate_card_id}', 'Broadcaster\RateCardManagementController@details')->name('rate_card.management.details');
        });

        /**
         * time belt management
         */
        Route::group(['prefix' => 'time-belt-management'], function () {
            Route::get('/', 'Broadcaster\TimeBeltManagementController@index')
                ->name('time.belt.management.index')->middleware('permission:view.inventory');
            Route::get('/data-table', 'Broadcaster\TimeBeltManagementController@formatToDatatable');
            Route::get('/details/{time_belt_id}', 'Broadcaster\TimeBeltManagementController@details')->name('time.belt.management.details');
        });

        /**
         * Schedule Management System
         */
        Route::group(['prefix' => 'schedule'], function () {
            Route::get('/weekly', 'Broadcaster\ScheduleController@getWeeklySchedule')->name('schedule.weekly');
        });

        /*
         * Campaign
         */
        Route::group(['prefix' => 'campaign'], function () {
            Route::get('/campaigns-list', 'Campaign\CampaignsController@campaignsList')
                ->name('campaign.list')->middleware('permission:view.campaign');
            Route::get('/active-campaigns', 'Campaign\CampaignsController@allActiveCampaigns')->name('campaign.all');
            Route::get('/all-active-campaigns/data', 'Campaign\CampaignsController@allActiveCampaignsData');
            Route::get('/all-campaigns/data', 'Campaign\CampaignsController@filteredCampaignsData');
            Route::get('/campaign-general-information', 'Campaign\CampaignsController@campaignGeneralInformation')
                ->name('campaign.get_campaign_general_information')->middleware('permission:create.campaign');
            Route::post('/campaign-general-information/store', 'Campaign\CampaignsController@storeCampaignGeneralInformation')->name('campaign.store_campaign_general_information');
            Route::get('/advert-slot/result/{id}', 'Campaign\CampaignsController@getAdSlotResult')->name('campaign.advert_slot');
            Route::get('/media-content/{id}', 'Campaign\CampaignsController@getMediaContent')->name('campaign.get_media_content');
            Route::get('/media-content/store/{id}', 'Campaign\CampaignsController@storeMediaContent')->name('campaign.store_media_content');
            Route::post('/remove-media-content/{client_id}/{upload_id}', 'Campaign\CampaignsController@removeMediaContent')->name('uploads.remove');
            Route::get('/adslot-selection/pre-process/{id}', 'Campaign\CampaignsController@preProcessAdslot')->name('campaign.adslot_preprocess');
            Route::get('/adslot-selection/{id}/{broadcaster}/{start_date}/{end_date}', 'Campaign\CampaignsController@getAdslotSelection')->name('campaign.adslot_selection');
            Route::get('/preselected-adslot/store/{id}', 'Campaign\CampaignsController@postPreselectedAdslot')->name('campaign.store_preselected_adslot');
            Route::get('/broadcaster-selection/{id}', 'Campaign\CampaignsController@selectBroadcaster')->name('campaign.broadcaster_select');
            Route::get('/checkout/{id}', 'Campaign\CampaignsController@checkout')->name('campaign.checkout');
            Route::get('/preselected-adslot/remove/{id}', 'Campaign\CampaignsController@removePreselectedAdslot')->name('preselected_adslot.remove');
            Route::post('/campaign-hold/{id}', 'Campaign\CampaignsController@postCampaign')->name('campaign.post_hold');
            Route::get('/campaign-on-hold/broadcaster/data', 'Campaign\CampaignsController@getCampaignOnHold')->name('broadcaster.campaign.hold');
            Route::get('/campaign-on-hold/agency/data', 'Campaign\CampaignsController@getCampaignOnHold')->name('agency.campaigns.hold');
            Route::post('/submit-to-broadcasters/{campaign_id}', 'Campaign\CampaignsController@submitWithOtherPaymentOption')->name('campaign.submit.other_payment');
            Route::post('/payment-process', 'Campaign\CampaignsController@submitWithCardPaymentOption')->name('broadcaster.pay');
            Route::post('/submit-to-broadcasters/agency/{campaign_id}', 'Campaign\CampaignsController@submitAgencyCampaign')->name('agency.campaign.update');
            Route::post('/update-campaign-budget', 'Campaign\CampaignsController@updateBudget')->name('campaign_budget.update');
            Route::get('/create/step3_1/{id}', 'Broadcaster\CampaignsController@storeStep3_1')->name('campaign.create3_1');
            Route::get('/create/step4/{id}/{broadcaster}/{start_date}/{end_date}', 'Broadcaster\CampaignsController@createStep4')->name('campaign.create4');
            Route::get('/cart/store', 'Broadcaster\CampaignsController@postPreselectedAdslot')->name('broadcaster_campaign.cart');

            Route::post('/submit-campaign/{id}', 'Broadcaster\CampaignsController@postCampaign')->name('submit.campaign');

            Route::get('/campaign-details/{id}', 'Broadcaster\CampaignsController@campaignDetails')
                ->name('broadcaster.campaign.details')->middleware('permission:view.campaign');

            Route::get('/{user_id}', 'Broadcaster\CampaignsController@filterByUser');

            Route::get('/media-channel/{campaign_id}', 'Broadcaster\CampaignsController@getMediaChannel');

            Route::get('/compliance-graph/broadcaster', 'Broadcaster\CampaignsController@complianceGraph');

            Route::get('/compliance-graph/filter/broadcaster', 'Broadcaster\CampaignsController@complianceFilter')->name('broadcaster.campaign_details.compliance');

            Route::post('/update-campaign/information/{campaign_id}', 'Broadcaster\CampaignsController@updateCampaignInformation')->name('broadcaster.campaign_information.update');
        });

        Route::get('/brand/get-industry', 'Campaign\CampaignsController@getBrandsIndustryAndSubIndustry');

        Route::get('file-update/{file_id}', 'MpoController@updateFiles')->name('file.change');

        /*
         * Broadcaster User Management
         */
        Route::group(['prefix' => 'broadcaster'], function () {
            Route::get('/users', 'BroadcasterAuthController@allUser')->name('broadcaster.user.all');
            Route::get('/user-data', 'BroadcasterAuthController@userData');
            Route::get('/users/create', 'BroadcasterAuthController@createUser')->name('broadcaster.user.create');
            Route::post('/user/create/store', 'BroadcasterAuthController@postBroadcasterUser')->name('broadcaster.post.user');
            Route::get('/user/delete/{id}', 'BroadcasterAuthController@deleteBroadcasterUser')->name('broadcaster_user.delete');
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

        /**
         * Hourly Ranges
         */

        Route::group(['prefix' => 'hourly-ranges'], function () {
            Route::get('/', 'HourlyController@index')->name('hourly.all');
        });

        Route::group(['prefix' => 'time'], function () {
            Route::get('/', 'SecondsController@index')->name('seconds.all');
        });

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

        Route::group(['prefix' => 'reports'], function () {
            Route::get('/', 'ReportController@index')->name('reports');
            Route::get('total-volume-campaigns/all-data', 'ReportController@HVCdata');
            Route::get('paid-invoices/all-data', 'ReportController@PIdata');
            Route::get('/periodic-sales/all', 'ReportController@psData');
            Route::get('/total-volume-of-campaign/all', 'ReportController@tvcData');
            Route::get('/high-day-parts/all', 'ReportController@hpdData');
            Route::get('/high-days/all', 'ReportController@hpdaysData');
        });

        //route to filter client details by a company owner with multiple publishers
        Route::get('/client-details/{client_id}', 'WalkinsController@filterByPublisher');

        //get all brands per walkins or clients
        Route::get('/client/get-brands/{id}', 'BrandsController@getBrandsWithClients');

        /**
         * Activity Log
         */

        Route::get('activity', [
            'as' => 'activity.index',
            'uses' => 'ActivityController@index',
        ]);

        Route::get('activity/user/{user}/log', [
            'as' => 'activity.user',
            'uses' => 'ActivityController@userActivity',
        ]);

        /**
         * Media Plan
         */
        Route::get('/media-plan1', 'MediaPlan\MediaPlanController@index');
        Route::get('/media-plan2', 'MediaPlan\MediaPlanController@index');

    });
};

Route::group(['domain' => env('SITE_URL', 'local.torch.docker.localhost')], $sspRoutes);
