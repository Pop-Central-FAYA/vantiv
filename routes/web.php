<?php

/**
 * Authentication
 */

Route::get('login', 'Auth\AuthController@getLogin')->name('login');
Route::post('login', 'Auth\AuthController@postLogin');

Route::get('/auth-broadcaster/signup', 'BroadcasterAuthController@getRegister')->name('broadcaster.register.form');
Route::post('/auth-broadcaster/signup/process', 'BroadcasterAuthController@postRegister')->name('broadcaster.signup');

Route::get('/auth-agent/signup', 'Agency\AgencyAuthController@getRegister')->name('agency.register.form');
Route::post('/auth-agent/signup/process', 'Agency\AgencyAuthController@postRegister')->name('agency.signup');

Route::get('/auth-advertiser/signup', 'Advertiser\AdvertiserAuthController@getRegister')->name('advertiser.register.form');
Route::post('/auth-advertiser/signup/process', 'Advertiser\AdvertiserAuthController@postRegister')->name('advertiser.signup');

Route::get('logout', [
    'as' => 'auth.logout',
    'uses' => 'Auth\AuthController@getLogout'
]);

Route::get('/forget-password', 'Auth\AuthController@getForgetPassword')->name('password.forgot');
Route::post('/forget-password/process', 'Auth\AuthController@processForgetPassword')->name('forget_password.process');
Route::get('/proceed/password-change/{token}','Auth\AuthController@processChangePassword');
Route::post('/change-password/process/{id_local}/{id_api}','Auth\AuthController@processGhangePassword')->name('change_password.process');

// Allow registration routes only if registration is enabled.
if (settings('reg_enabled')) {
    Route::get('register', 'Auth\AuthController@getRegister');
    Route::post('register', 'Auth\AuthController@postRegister');
    Route::get('register/confirmation/{token}', [
        'as' => 'register.confirm-email',
        'uses' => 'Auth\AuthController@confirmEmail'
    ]);
}

// Register password reset routes only if it is enabled inside website settings.
if (settings('forgot_password')) {
    Route::get('password/remind', 'Auth\PasswordController@forgotPassword');
    Route::post('password/remind', 'Auth\PasswordController@sendPasswordReminder');
    Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
    Route::post('password/reset', 'Auth\PasswordController@postReset');
}

/**
 * Two-Factor Authentication
 */
if (settings('2fa.enabled')) {
    Route::get('auth/two-factor-authentication', [
        'as' => 'auth.token',
        'uses' => 'Auth\AuthController@getToken'
    ]);

    Route::post('auth/two-factor-authentication', [
        'as' => 'auth.token.validate',
        'uses' => 'Auth\AuthController@postToken'
    ]);
}

/**
 * Social Login
 */
Route::get('auth/{provider}/login', [
    'as' => 'social.login',
    'uses' => 'Auth\SocialAuthController@redirectToProvider',
    'middleware' => 'social.login'
]);

Route::get('/test', function () {
    return view('layouts/app');
});

Route::get('auth/{provider}/callback', 'Auth\SocialAuthController@handleProviderCallback');

Route::get('auth/twitter/email', 'Auth\SocialAuthController@getTwitterEmail');
Route::post('auth/twitter/email', 'Auth\SocialAuthController@postTwitterEmail');

Route::get('/error', 'InstallController@apiError')->name('errors');

Route::get('register/verify/{token}', 'Auth\AuthController@verifyToken');

Route::get('/reg-admin', 'AdminAuthController@getAdmin')->name('admin.register.get');

Route::post('/admin/post', 'AdminAuthController@postRegister')->name('admin.post');

Route::group(['middleware' => 'auth'], function () {


//        Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay');
//
//        Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');

    /*
     * Super Admin
     */

    Route::group(['prefix' => 'industry'], function () {
       Route::get('/', 'IndustriesController@index')->name('industry.index');
       Route::get('/create', 'IndustriesController@create')->name('industry.create');
       Route::post('/store', 'IndustriesController@store')->name('industry.store');
       Route::get('/edit/{code}', 'IndustriesController@edit')->name('industry.edit');
       Route::post('/update/{id}', 'IndustriesController@update')->name('industry.update');
       Route::get('/delete/{id}', 'IndustriesController@delete')->name('industry.delete');
       Route::get('/get-data', 'IndustriesController@getData');

       Route::group(['prefix' => 'sub-industry'], function () {
          Route::get('/', 'IndustriesController@indexSubIndustry')->name('sub_industry.index');
          Route::get('/get-data', 'IndustriesController@subIndustryData');
          Route::get('/create', 'IndustriesController@indexCreateIndustry')->name('sub_industry.create');
          Route::post('/store', 'IndustriesController@storeSubIndustry')->name('sub_industry.store');
          Route::get('/delete/{id}', 'IndustriesController@deleteSubIndustry')->name('sub_industry.delete');
          Route::get('/edit/{code}', 'IndustriesController@editSubIndustry')->name('sub_industry.edit');
          Route::post('/update/{id}', 'IndustriesController@updateSubIndustry')->name('sub_industry.update');
       });
    });

    Route::group(['prefix' => 'regions'], function() {
        Route::get('/', 'RegionsController@index')->name('admin.region.index');
        Route::get('/data', 'RegionsController@data');
        Route::get('/create', 'RegionsController@create')->name('admin.region.create');
        Route::post('/store', 'RegionsController@store')->name('admin.region.store');
        Route::post('/update/{id}', 'RegionsController@update')->name('admin.region.update');
        Route::get('/delete/{id}', 'RegionsController@delete')->name('admin.region.delete');
    });

    Route::group(['prefix' => 'target-audiences'], function() {
        Route::get('/', 'TargetAudienceController@index')->name('target_audience.index');
        Route::get('/data', 'TargetAudienceController@getData');
        Route::get('/create', 'TargetAudienceController@create')->name('admin.target_audience.create');
        Route::post('/store', 'TargetAudienceController@store')->name('admin.target_audience.store');
        Route::post('/update/{id}', 'TargetAudienceController@update')->name('admin.target_audience.update');
        Route::get('/delete/{id}', 'TargetAudienceController@delete')->name('admin.target_audience.delete');
    });

    Route::group(['prefix' => 'admin-broadcaster'], function() {
       Route::get('/', 'BroadcasterManagementController@index')->name('admin.broadcaster.index');
       Route::get('/broadcaster-data', 'BroadcasterManagementController@braodcasterData');
       Route::get('/details/{id}', 'BroadcasterManagementController@broadcasterDetails')->name('admin.broadcaster.details');
       Route::get('/inventory/{id}', 'BroadcasterManagementController@getInventory')->name('admin.broadcaster.upload_inventory');
       Route::post('/inventory/store/{id}', 'BroadcasterManagementController@storeInventory')->name('upload_inventory.store');
    });

    Route::group(['prefix' => 'day-parts'], function() {
       Route::get('/', 'AdminDayPartsController@index')->name('admin.dayparts');
       Route::get('/data', 'AdminDayPartsController@getData');
       Route::get('/create', 'AdminDayPartsController@create')->name('admin.daypart.create');
       Route::post('/store', 'AdminDayPartsController@store')->name('admin.daypart.store');
       Route::post('/update/{id}', 'AdminDayPartsController@update')->name('admin.daypart.update');
       Route::get('/delete/{id}', 'AdminDayPartsController@delete')->name('admin.daypart.delete');
    });


    /*
    * Campaign
    */
    Route::group(['prefix' => 'campaign'], function(){
        Route::get('/', 'CampaignsController@index')->name('campaign.all');
        Route::get('/setup', 'CampaignsController@setup')->name('campaign.setup');
        Route::get('/create', 'CampaignsController@create')->name('campaign.create');
        Route::get('/create/{walkins}/step2', 'CampaignsController@createStep2')->name('campaign.create2');
        Route::get('/create/{walkins}/step3', 'CampaignsController@createStep3')->name('campaign.create3');
        Route::get('/create/{walkins}/step4', 'CampaignsController@createStep4')->name('campaign.create4');
        Route::get('/create/{walkins}/step4/1', 'CampaignsController@createStep4_1')->name('campaign.create4_1');
        Route::get('/create/{walkins}/step4/2', 'CampaignsController@createStep4_2')->name('campaign.create4_2');
        Route::get('/create/{walkins}/step4/3', 'CampaignsController@createStep4_3')->name('campaign.create4_3');
        Route::get('/create/{walkins}/step5', 'CampaignsController@createStep5')->name('campaign.create5');
        Route::get('/upload/delete/{walkins}/{id}', 'CampaignsController@deleteUploads')->name('uploads.delete');
        Route::get('/create/{walkins}/step6', 'CampaignsController@createStep6')->name('campaign.create6');
        Route::get('/create/{walkins}/step7', 'CampaignsController@createStep7')->name('campaign.create7');
        Route::get('/create/step8', 'CampaignsController@createStep8')->name('campaign.create8');
        Route::get('/create/step9', 'CampaignsController@createStep9')->name('campaign.create9');
        Route::post('/create/{walkins}/step2/store', 'CampaignsController@postStep2')->name('campaign.store2');
        Route::post('/create/{walkins}/step3/store', 'CampaignsController@postStep3')->name('campaign.store3');
        Route::post('/create/{walkins}/step4/store', 'CampaignsController@postStep4')->name('campaign.store4');
        Route::post('/create/{walkins}/step4/1/store', 'CampaignsController@postStep4_1')->name('campaign.store4_1');
        Route::post('/create/{walkins}/step4/2/store', 'CampaignsController@postStep4_2')->name('campaign.store4_2');
        Route::post('/create/{walkins}/step4/3/store', 'CampaignsController@postStep4_3')->name('campaign.store4_3');
        Route::post('/create/{walkins}/step5/uploads/store', 'CampaignsController@postStep5')->name('campaign.store5.uploads');
        Route::post('/create/{walkins}/step6/store', 'CampaignsController@postStep6')->name('campaign.store6');
        Route::get('/create/{walkins}/step7/get', 'CampaignsController@getStep7')->name('campaign.store7');
        Route::post('/store/{walkins}', 'CampaignsController@store')->name('campaign.store');
        Route::get('/add-to-cart', 'CampaignsController@postCart')->name('store.cart');
        Route::get('/checkout/{walkins}', 'CampaignsController@getCheckout')->name('checkout');
        Route::post('/submit-campaign/{walkins}', 'CampaignsController@postCampaign')->name('submit.campaign');
        Route::get('/remove-campaigns/{id}', 'CampaignsController@removeCart')->name('cart.remove');
        Route::post('/remove-media/{walkins}/{id}', 'CampaignsController@removeMedia')->name('uploads.remove');

        Route::post('/payment-process', 'CampaignsController@payCampaign')->name('broadcaster.pay');

        Route::get('/all-campaign/data', 'CampaignsController@getAllData');

        Route::get('/campaign-details/{id}', 'CampaignsController@campaignDetails')->name('broadcaster.campaign.details');
    });

    /*
     * Broadcaster User Management
     */
    Route::group(['prefix' => 'broadcaster'], function (){
        Route::get('/users', 'BroadcasterAuthController@allUser')->name('broadcaster.user.all');
        Route::get('/user-data', 'BroadcasterAuthController@userData');
        Route::get('/users/create', 'BroadcasterAuthController@createUser')->name('broadcaster.user.create');
        Route::post('/user/create/store', 'BroadcasterAuthController@postBroadcasterUser')->name('broadcaster.post.user');
        Route::get('/user/delete/{id}', 'BroadcasterAuthController@deleteBroadcasterUser')->name('broadcaster_user.delete');
    });

    Route::group(['prefix' => 'broadcaster-user/campaign'], function (){
       Route::get('/create/{walkins}/{broadcaster}/{broadcaster_user}/step1', 'BroadcasterUserCampaignsController@createStep1')->name('broadcaster_user.campaign.create1');
       Route::post('/create/{walkins}/{broadcaster}/{broadcaster_user}/step1/store', 'BroadcasterUserCampaignsController@postStore1')->name('broadcaster.user.campaign.store1');
       Route::get('/create/{walkins}/{broadcaster}/{broadcaster_user}/step2', 'BroadcasterUserCampaignsController@createStep2')->name('broadcaster_user.campaigns.create2');
       Route::post('/create/{walkins}/{broadcaster}/{broadcaster_user}/step2/store', 'BroadcasterUserCampaignsController@postStore2')->name('broadcaster.user.campaign.store2');
       Route::get('/create/{walkins}/{broadcaster}/{broadcaster_user}/step3', 'BroadcasterUserCampaignsController@createStep3')->name('broadcaster.user.campaign.step3');
       Route::post('/create/{walkins}/{broadcaster}/{broadcaster_user}/step3/store', 'BroadcasterUserCampaignsController@postStore3')->name('broadcaster.user.campaign.store3');
       Route::post('/create/{walkins}/{broadcaster}/{broadcaster_user}/step3_1/store', 'BroadcasterUserCampaignsController@postStore3_1')->name('broadcaster.user.campaign.store3_1');
       Route::get('/create/{walkins}/{broadcaster}/{broadcaster_user}/step4', 'BroadcasterUserCampaignsController@createStep4')->name('broadcaster.user.campaign.create4');
       Route::get('/create/{walkins}/{broadcaster}/{broadcaster_user}/step5', 'BroadcasterUserCampaignsController@createStep5')->name('broadcaster.user.campaign.store5');
       Route::get('/checkout/{walkins}/{broadcaster}/{broadcaster_user}', 'BroadcasterUserCampaignsController@getCheckout')->name('broadcaster.user.checkout');
       Route::post('/submit/{walkins}/{broadcaster}/{broadcaster_user}', 'BroadcasterUserCampaignsController@submitCampaign')->name('broadcaster.user.submit.campaign');
       Route::post('/payment-process/{walkins}/{broadcaster}/{broadcaster_user}', 'BroadcasterUserCampaignsController@cardPayment')->name('broadcaster.user.pay');
       Route::get('/all-campaigns', 'BroadcasterUserCampaignsController@index')->name('broadcaster.user.campaign.all');
       Route::get('/campaign-data', 'BroadcasterUserCampaignsController@campaignData');
       Route::get('/campaign-details/{id}', 'BroadcasterUserCampaignsController@campaignDetails')->name('user.broadcaster.campaign.details');
       Route::get('/post-cart/{walkins}/{broadcaster}/{broadcaster_user}', 'BroadcasterUserCampaignsController@postCart')->name('broadcaster_user.post.cart');
       Route::get('/set-up', 'BroadcasterUserCampaignsController@setup')->name('broadcaster_user.campaign.setup');
    });

    Route::group(['prefix' => 'broadcaster-user/reports'], function () {
        Route::get('/', 'BroadcasterUserReportsController@index')->name('broadcaster.user.reports');
        Route::get('total-volume-campaigns/all-data', 'BroadcasterUserReportsController@HVCdata');
        Route::get('paid-invoices/all-data', 'BroadcasterUserReportsController@PIdata');
        Route::get('/periodic-sales/all', 'BroadcasterUserReportsController@psData');
        Route::get('/total-volume-of-campaign/all', 'BroadcasterUserReportsController@tvcData');
        Route::get('/high-day-parts/all', 'BroadcasterUserReportsController@hpdData');
        Route::get('/high-days/all', 'BroadcasterUserReportsController@hpdaysData');
    });

    Route::get('/brand/get-industry', 'CampaignsController@getIndustrySubIndustry');

    /*
     * WalkIns Management
     */

    Route::group(['prefix' => 'brands'], function () {
        Route::get('/', 'BrandsController@index')->name('brand.all');
        Route::get('/create', 'BrandsController@create')->name('brand.create');
        Route::post('/create/store', 'BrandsController@store')->name('brand.store');
        Route::post('/brands/edit/{id}', 'BrandsController@update')->name('brands.update');
        Route::get('/brands/delete/{id}', 'BrandsController@delete')->name('brands.delete');
        Route::get('/search-brands', 'BrandsController@search')->name('broadcasters.brands.search');
    });

    Route::group(['prefix' => 'walk-in'], function () {
        Route::get('/', 'WalkinsController@index')->name('walkins.all');
        Route::get('/all-walk-in/data', 'WalkinsController@walkinsData');
        Route::get('/create', 'WalkinsController@create')->name('walkins.create');
        Route::post('/store', 'WalkinsController@store')->name('walkins.store');
        Route::get('/delete/{id}', 'WalkinsController@delete')->name('walkins.delete');
        Route::get('/brand', 'WalkinsController@getSubIndustry');
    });

    /**
     * Sectors
     */

    Route::get('sectors', ['as' => 'sector.index', 'uses' => 'SectorController@index']);

    Route::get('sector/create', ['as' => 'sector.create', 'uses' => 'SectorController@create'
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

    Route::get('/', ['as' => 'dashboard', 'uses' => 'DashboardController@index']);

    Route::get('/clients-dashboard', ['as' => 'agency.dashboard', 'uses' => 'DashboardController@clientDashboard']);

    /**
     * Adslot
     */
    Route::group(['prefix' => '/adslot'], function () {
        Route::get('/', 'AdslotController@index')->name('adslot.all');
        Route::get('/adslot-data', 'AdslotController@adslotData');
        Route::get('/create', 'AdslotController@create')->name('adslot.create');
        Route::post('/store', 'AdslotController@store')->name('adslot.store');
        Route::post('/update/{broadcaster}/{adslot}', 'AdslotController@update')->name('adslot.update');
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
    Route::get('discounts', ['as' => 'discount.index', 'uses' => 'DiscountController@index']);
    Route::post('discount/store', ['as' => 'discount.store', 'uses' => 'DiscountController@store']);
    Route::post('discount/{discount}/update', ['as' => 'discount.update', 'uses' => 'DiscountController@update']);
    Route::get('discount/{discount}/delete', ['as' => 'discount.delete', 'uses' => 'DiscountController@destroy']);

    /**
     * MPOs
     */
    Route::group(['prefix' => 'mpos'], function () {
        Route::get('/pending_mpos_data', 'MpoController@pending_mpos_data');
        Route::get('all', 'MpoController@index')->name('all-mpos');
        Route::get('pending', 'MpoController@pending_mpos')->name('pending-mpos');
        Route::post('approve/{is_file_accepted}/{file_code}/{rejection_reason}', ['as' => 'files.update', 'uses' => 'MpoController@update_file']);
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

    Route::group(['prefix' => 'agency'], function() {
        Route::group(['prefix' => 'campaigns'], function() {
            Route::get('/all-campaigns', 'Agency\CampaignsController@index')->name('agency.campaign.all');
            Route::get('/all-campaign/data', 'Agency\CampaignsController@getData');
            Route::get('/all-clients', 'Agency\CampaignsController@allClient')->name('agency.campaign.create');
            Route::get('/all-client/data', 'Agency\CampaignsController@clientData');
            Route::get('/campaign/step1/{id}', 'Agency\CampaignsController@getStep1')->name('agency_campaign.step1');
            Route::post('/campaign/step1/store/{id}', 'Agency\CampaignsController@postStep1')->name('agency_campaign.store1');
            Route::get('/campaigns/step2/{id}', 'Agency\CampaignsController@getStep2')->name('agency_campaign.step2');
            Route::get('/campaign/step3/{id}', 'Agency\CampaignsController@getStep3')->name('agency_campaign.step3');
            Route::post('/campaign/step3/store/{id}', 'Agency\CampaignsController@postStep3')->name('agency_campaign.store3');
            Route::get('/campaign/step3/1/{id}', 'Agency\CampaignsController@getStep3_1')->name('agency_campaign.step3_1');
            Route::post('/campaign/step3/store/1/{id}', 'Agency\CampaignsController@postStep3_1')->name('agency_campaign.store3_1');
            Route::get('/campaign/step3/2/{id}', 'Agency\CampaignsController@getStep3_2')->name('agency_campaign.step3_2');
            Route::post('/campaign/step3/store/2/{id}/{broadcaster}', 'Agency\CampaignsController@postStep3_2')->name('agency_campaign.store3_2');
            Route::get('/campaign/step3/3/{id}/{broadcaster}', 'Agency\CampaignsController@getStep3_3')->name('agency_campaign.step3_3');
            Route::post('/campaign/step3/store/3/{id}/{broadcaster}', 'Agency\CampaignsController@postStep3_3')->name('agency_campaign.store3_3');
            Route::post('/campaign/step3/store/new-uploads/{id}/{broadcaster}', 'Agency\CampaignsController@postNewUploads')->name('new.upload');
            Route::get('/camaigns/uploads/delete/{upload_id}/{id}', 'Agency\CampaignsController@deleteUpload')->name('agency.uploads.delete');
            Route::get('/review-uploads/{id}/{broadcaster}', 'Agency\CampaignsController@reviewUploads')->name('agency_campaign.review_uploads');
            Route::get('/campaign/step4/{id}/{broadcaster}', 'Agency\CampaignsController@getStep4')->name('agency_campaign.step4');
            Route::get('/campaigns/cart/store', 'Agency\CampaignsController@postCart')->name('agency_campaign.cart');
            Route::get('/campaign/checkout/{id}', 'Agency\CampaignsController@checkout')->name('agency_campaign.checkout');
            Route::get('/cart/remove/{id}', 'Agency\CampaignsController@removeCart')->name('agency_cart.remove');
            Route::post('/campaign/submit/{id}', 'Agency\CampaignsController@postCampaign')->name('agency_submit.campaign');

            Route::get('/campaign-details/{id}', 'Agency\CampaignsController@getDetails')->name('agency.campaign.details');
            Route::get('/mpo-details/{id}', 'Agency\CampaignsController@mpoDetails')->name('agency.mpo.details');
        });

        Route::get('/agency-dashboard/periodic-sales', 'DashboardController@filterByBroad')->name('agency.dashboard.broad');
        Route::get('/agency-dashboard/periodic-brand', 'DashboardController@filterByBrand')->name('agency.dashboard.data');
        Route::get('/agency/percentage-periodic', 'DashboardController@filterByMonth')->name('agency.month');


        /**
         * Clients
         */
        Route::group(['prefix' => 'clients'], function () {
            Route::get('/', 'ClientsController@index')->name('clients.all');
            Route::any('/create', 'ClientsController@create')->name('clients.create');
            Route::get('list', 'ClientsController@clients')->name('clients.list');
            Route::get('/client/{client_id}', 'ClientsController@clientShow')->name('client.show');
            Route::get('/client/brand/{id}', 'ClientsController@getClientBrands')->name('client_brands');
        });

        Route::group(['prefix' => 'invoices'], function () {
            Route::get('/all', 'InvoiceController@all')->name('invoices.all');
            Route::get('/pending', 'InvoiceController@pending')->name('invoices.pending');
            Route::post('/{invoice_id}/update', 'InvoiceController@approveInvoice')->name('invoices.update');
        });

        Route::group(['prefix' => 'wallets'], function(){
            Route::get('/wallet/credit', 'Agency\WalletsController@create')->name('agency_wallet.create');
            Route::get('/wallet-statement', 'Agency\WalletsController@index')->name('agency_wallet.statement');
            Route::post('/wallet/amount', 'Agency\WalletsController@getAmount')->name('wallet.amount');
            Route::get('/wallet/amount/pay', 'Agency\WalletsController@getPay')->name('amount.pay');
            Route::post('/pay', 'Agency\WalletsController@pay')->name('pay');
            Route::get('/get-wallet/data', 'Agency\WalletsController@getData');
        });

        Route::group(['prefix' => 'reports'], function(){
            Route::get('/', 'Agency\ReportsController@index')->name('reports.index');
            Route::get('/campaign/all-data', 'Agency\ReportsController@getCampaign');
            Route::get('/revenue/all-data', 'Agency\ReportsController@getRevenue');
//                Route::get('/client-filter/campaign', 'Agency\ReportsController@filterCampaignClient')->name('filter.client');
        });
    });

    Route::group(['prefix' => 'wallets'], function(){
        Route::get('/wallet/credit', 'Agency\WalletsController@create')->name('wallet.create');
        Route::get('/wallet-statement', 'Agency\WalletsController@index')->name('wallet.statement');
        Route::post('/wallet/amount', 'Agency\WalletsController@getAmount')->name('wallet.amount');
        Route::get('/wallet/amount/pay', 'Agency\WalletsController@getPay')->name('amount.pay');
        Route::post('/pay', 'Agency\WalletsController@pay')->name('pay');
        Route::get('/get-wallet/data', 'Agency\WalletsController@getData');
    });

    Route::group(['prefix' => 'client-brands'], function () {
        Route::get('/all-brands', 'ClientBrandsController@index')->name('agency.brand.all');
        Route::get('/create-brand', 'ClientBrandsController@create')->name('agency.brand.create');
        Route::post('/create/store', 'ClientBrandsController@store')->name('agency.brand.store');
        Route::post('/brands/edit/{id}', 'ClientBrandsController@update')->name('agency.brands.update');
        Route::get('/brands/delete/{id}', 'ClientBrandsController@delete')->name('agency.brands.delete');
        Route::get('/search-result', 'ClientBrandsController@search')->name('brands.search.user');
    });

    Route::group(['prefix' => 'clients'], function () {
       Route::get('/campaigns', 'Clients\CampaignsController@index')->name('client.campaign.all');
       Route::get('/campaigns-data', 'Clients\CampaignsController@getData');
       Route::get('/campaigns/details/{id}', 'Clients\CampaignsController@getDetails')->name('client.campaign.details');
       Route::get('/reports', 'Clients\ReportsController@index')->name('client.report.index');
    });

    Route::group(['prefix' => 'advertiser'], function () {
        Route::group(['prefix' => 'campaigns'], function() {
            Route::get('/', 'Advertiser\CampaignsController@firstPage')->name('advertiser.first_page');
            Route::get('/all-campaigns', 'Advertiser\CampaignsController@index')->name('advertiser.campaign.all');
            Route::get('/all-campaign/data', 'Advertiser\CampaignsController@getData');
            Route::get('/all-clients', 'Advertiser\CampaignsController@allClient')->name('advertiser.campaign.create');
            Route::get('/campaign/step1/{id}', 'Advertiser\CampaignsController@getStep1')->name('advertiser_campaign.step1');
            Route::post('/campaign/step1/store/{id}', 'Advertiser\CampaignsController@postStep1')->name('advertiser_campaign.store1');
            Route::get('/campaigns/step2/{id}', 'Advertiser\CampaignsController@getStep2')->name('advertiser_campaign.step2');
            Route::get('/campaign/step3/{id}', 'Advertiser\CampaignsController@getStep3')->name('advertiser_campaign.step3');
            Route::post('/campaign/step3/store/{id}}', 'Advertiser\CampaignsController@postStep3')->name('advertiser_campaign.store3');
            Route::get('/campaign/step3/1/{id}}', 'Advertiser\CampaignsController@getStep3_1')->name('advertiser_campaign.step3_1');
            Route::post('/campaign/step3/store/1/{id}', 'Advertiser\CampaignsController@postStep3_1')->name('advertiser_campaign.store3_1');
            Route::get('/campaign/step3/2/{id}', 'Advertiser\CampaignsController@getStep3_2')->name('advertiser_campaign.step3_2');
            Route::post('/campaign/step3/store/2/{id}', 'Advertiser\CampaignsController@postStep3_2')->name('advertiser_campaign.store3_2');
            Route::get('/campaign/step3/3/{id}/{broadcaster}', 'Advertiser\CampaignsController@getStep3_3')->name('advertiser_campaign.step3_3');
            Route::post('/campaign/step3/store/3/{id}/{broadcaster}', 'Advertiser\CampaignsController@postStep3_3')->name('advertiser_campaign.store3_3');
            Route::post('/campaign/step3/store/new-uploads/{id}/{broadcaster}', 'Advertiser\CampaignsController@postNewUploads')->name('new.upload');
            Route::get('/camaigns/uploads/delete/{upload_id}/{id}', 'Advertiser\CampaignsController@deleteUpload')->name('advertiser.uploads.delete');
            Route::get('/review-uploads/{id}/{broadcaster}', 'Advertiser\CampaignsController@reviewUploads')->name('advertiser_campaign.review_uploads');
            Route::get('/campaign/step4/{id}/{broadcaster}', 'Advertiser\CampaignsController@getStep4')->name('advertiser_campaign.step4');
            Route::get('/campaigns/cart/store', 'Advertiser\CampaignsController@postCart')->name('advertiser_campaign.cart');
            Route::get('/campaign/checkout/{id}', 'Advertiser\CampaignsController@checkout')->name('advertiser_campaign.checkout');
            Route::get('/cart/remove/{id}', 'Advertiser\CampaignsController@removeCart')->name('advertiser_cart.remove');
            Route::post('/campaign/submit/{id}', 'Advertiser\CampaignsController@postCampaign')->name('advertiser_submit.campaign');

            Route::get('/campaign-details/{id}', 'Advertiser\CampaignsController@getDetails')->name('advertiser.campaign.details');
            Route::get('/mpo-details/{id}', 'Advertiser\CampaignsController@mpoDetails')->name('advertiser.mpo.details');
        });

        Route::group(['prefix' => 'reports'], function (){
            Route::get('/', 'Advertiser\ReportsController@index')->name('advertiser.report.index');
            Route::get('/campaign/all-data', 'Advertiser\ReportsController@getCampaign');
            Route::get('/revenue/all-data', 'Advertiser\ReportsController@getRevenue');
        });

        Route::group(['prefix' => 'invoices'], function () {
            Route::get('/all', 'Advertiser\InvoiceController@all')->name('advertisers.invoices.all');
            Route::get('/pending', 'Advertiser\InvoiceController@pending')->name('advertisers.invoices.pending');
        });

        Route::get('/advertiser-dashboard/periodic-sales', 'DashboardController@filterByAdvertiserBroad')->name('advertiser.dashboard.broad');
        Route::get('/advertiser-dashboard/periodic-brand', 'DashboardController@filterByAdvertiserBrand')->name('advertiser.dashboard.data');
        Route::get('/advertiser/percentage-periodic', 'DashboardController@filterByAdvertiserMonth')->name('advertiser.month');

    });
});

/**
 * User Profile
 */


Route::get('user/profile', [
    'as' => 'user.profile',
    'uses' => 'ProfileManagementsController@index'
]);

Route::get('profile/activity', [
    'as' => 'profile.activity',
    'uses' => 'ProfileController@activity'
]);

Route::post('profile/details/update', [
    'as' => 'profile.update.details',
    'uses' => 'ProfileManagementsController@updateDetails'
]);

Route::post('profile/avatar/update', [
    'as' => 'profile.update.avatar',
    'uses' => 'ProfileController@updateAvatar'
]);

Route::post('profile/avatar/update/external', [
    'as' => 'profile.update.avatar-external',
    'uses' => 'ProfileController@updateAvatarExternal'
]);

Route::put('profile/login-details/update', [
    'as' => 'profile.update.login-details',
    'uses' => 'ProfileController@updateLoginDetails'
]);

Route::put('profile/social-networks/update', [
    'as' => 'profile.update.social-networks',
    'uses' => 'ProfileController@updateSocialNetworks'
]);

Route::post('profile/two-factor/enable', [
    'as' => 'profile.two-factor.enable',
    'uses' => 'ProfileController@enableTwoFactorAuth'
]);

Route::post('profile/two-factor/disable', [
    'as' => 'profile.two-factor.disable',
    'uses' => 'ProfileController@disableTwoFactorAuth'
]);

Route::get('profile/sessions', [
    'as' => 'profile.sessions',
    'uses' => 'ProfileController@sessions'
]);

Route::delete('profile/sessions/{session}/invalidate', [
    'as' => 'profile.sessions.invalidate',
    'uses' => 'ProfileController@invalidateSession'
]);

/**
 * User Management
 */
Route::get('user', [
    'as' => 'user.list',
    'uses' => 'UsersController@index'
]);

Route::get('user/create', [
    'as' => 'user.create',
    'uses' => 'UsersController@create'
]);

Route::post('user/create', [
    'as' => 'user.store',
    'uses' => 'UsersController@store'
]);

Route::get('user/{user}/show', [
    'as' => 'user.show',
    'uses' => 'UsersController@view'
]);

Route::get('user/{user}/edit', [
    'as' => 'user.edit',
    'uses' => 'UsersController@edit'
]);

Route::put('user/{user}/update/details', [
    'as' => 'user.update.details',
    'uses' => 'UsersController@updateDetails'
]);

Route::put('user/{user}/update/login-details', [
    'as' => 'user.update.login-details',
    'uses' => 'UsersController@updateLoginDetails'
]);

Route::delete('user/{user}/delete', [
    'as' => 'user.delete',
    'uses' => 'UsersController@delete'
]);

Route::post('user/{user}/update/avatar', [
    'as' => 'user.update.avatar',
    'uses' => 'UsersController@updateAvatar'
]);

Route::post('user/{user}/update/avatar/external', [
    'as' => 'user.update.avatar.external',
    'uses' => 'UsersController@updateAvatarExternal'
]);

Route::post('user/{user}/update/social-networks', [
    'as' => 'user.update.socials',
    'uses' => 'UsersController@updateSocialNetworks'
]);

Route::get('user/{user}/sessions', [
    'as' => 'user.sessions',
    'uses' => 'UsersController@sessions'
]);

Route::delete('user/{user}/sessions/{session}/invalidate', [
    'as' => 'user.sessions.invalidate',
    'uses' => 'UsersController@invalidateSession'
]);

Route::post('user/{user}/two-factor/enable', [
    'as' => 'user.two-factor.enable',
    'uses' => 'UsersController@enableTwoFactorAuth'
]);

Route::post('user/{user}/two-factor/disable', [
    'as' => 'user.two-factor.disable',
    'uses' => 'UsersController@disableTwoFactorAuth'
]);

/**
 * Roles & Permissions
 */

Route::get('role', [
    'as' => 'role.index',
    'uses' => 'RolesController@index'
]);

Route::get('role/create', [
    'as' => 'role.create',
    'uses' => 'RolesController@create'
]);

Route::post('role/store', [
    'as' => 'role.store',
    'uses' => 'RolesController@store'
]);

Route::get('role/{role}/edit', [
    'as' => 'role.edit',
    'uses' => 'RolesController@edit'
]);

Route::put('role/{role}/update', [
    'as' => 'role.update',
    'uses' => 'RolesController@update'
]);

Route::delete('role/{role}/delete', [
    'as' => 'role.delete',
    'uses' => 'RolesController@delete'
]);


Route::post('permission/save', [
    'as' => 'permission.save',
    'uses' => 'PermissionsController@saveRolePermissions'
]);

Route::resource('permission', 'PermissionsController');

/**
 * Settings
 */

Route::get('settings', [
    'as' => 'settings.general',
    'uses' => 'SettingsController@general',
    'middleware' => 'permission:settings.general'
]);

Route::post('settings/general', [
    'as' => 'settings.general.update',
    'uses' => 'SettingsController@update',
    'middleware' => 'permission:settings.general'
]);

Route::get('settings/auth', [
    'as' => 'settings.auth',
    'uses' => 'SettingsController@auth',
    'middleware' => 'permission:settings.auth'
]);

Route::post('settings/auth', [
    'as' => 'settings.auth.update',
    'uses' => 'SettingsController@update',
    'middleware' => 'permission:settings.auth'
]);

// Only allow managing 2FA if AUTHY_KEY is defined inside .env file
if (env('AUTHY_KEY')) {
    Route::post('settings/auth/2fa/enable', [
        'as' => 'settings.auth.2fa.enable',
        'uses' => 'SettingsController@enableTwoFactor',
        'middleware' => 'permission:settings.auth'
    ]);

    Route::post('settings/auth/2fa/disable', [
        'as' => 'settings.auth.2fa.disable',
        'uses' => 'SettingsController@disableTwoFactor',
        'middleware' => 'permission:settings.auth'
    ]);
}

Route::post('settings/auth/registration/captcha/enable', [
    'as' => 'settings.registration.captcha.enable',
    'uses' => 'SettingsController@enableCaptcha',
    'middleware' => 'permission:settings.auth'
]);

Route::post('settings/auth/registration/captcha/disable', [
    'as' => 'settings.registration.captcha.disable',
    'uses' => 'SettingsController@disableCaptcha',
    'middleware' => 'permission:settings.auth'
]);

Route::get('settings/notifications', [
    'as' => 'settings.notifications',
    'uses' => 'SettingsController@notifications',
    'middleware' => 'permission:settings.notifications'
]);

Route::post('settings/notifications', [
    'as' => 'settings.notifications.update',
    'uses' => 'SettingsController@update',
    'middleware' => 'permission:settings.notifications'
]);

/**
 * Activity Log
 */

Route::get('activity', [
    'as' => 'activity.index',
    'uses' => 'ActivityController@index'
]);

Route::get('activity/user/{user}/log', [
    'as' => 'activity.user',
    'uses' => 'ActivityController@userActivity'
]);

