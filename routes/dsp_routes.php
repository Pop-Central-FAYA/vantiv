<?php

Route::group(['domain' => 'vantage'. substr($_SERVER['SERVER_NAME'], 7)], function() {

         Route::get('login', 'Auth\AuthController@getLogin')->name('login');
         Route::post('login', 'Auth\AuthController@postLogin')->name('post.login');
      
        Route::group(['prefix' => 'campaigns'], function() {
            Route::get('/all-campaigns/active', 'Agency\CampaignsController@index')->name('agency.campaign.all');
            Route::get('/all-campaign/data', 'Agency\CampaignsController@getData');
            Route::get('/all-clients', 'Agency\CampaignsController@allClient')->name('agency.campaign.create');
            Route::get('/all-client/data', 'Agency\CampaignsController@clientData');
            Route::get('/campaign/step1', 'Agency\CampaignsController@getStep1')->name('agency_campaign.step1');
            Route::post('/campaign/step1/store', 'Agency\CampaignsController@postStep1')->name('agency_campaign.store1');
            Route::get('/campaigns/step2/{id}', 'Agency\CampaignsController@getStep2')->name('agency_campaign.step2');
            Route::get('/campaign/step3/{id}', 'Agency\CampaignsController@getStep3')->name('agency_campaign.step3');
            Route::get('/campaign/step3/store/{id}', 'Agency\CampaignsController@postStep3')->name('agency_campaign.store3');
            Route::get('/campaign/step3/1/{id}', 'Agency\CampaignsController@getStep3_1')->name('agency_campaign.step3_1');
            Route::get('/campaign/step3/store/1/{id}', 'Agency\CampaignsController@postStep3_1')->name('agency_campaign.store3_1');
            Route::get('/campaign/step3/2/{id}', 'Agency\CampaignsController@getStep3_2')->name('agency_campaign.step3_2');
            Route::post('/campaign/step3/store/2/{id}/{broadcaster}', 'Agency\CampaignsController@postStep3_2')->name('agency_campaign.store3_2');
            Route::get('/campaign/step3/3/{id}/{broadcaster}', 'Agency\CampaignsController@getStep3_3')->name('agency_campaign.step3_3');
            Route::post('/campaign/step3/store/3/{id}/{broadcaster}', 'Agency\CampaignsController@postStep3_3')->name('agency_campaign.store3_3');
            Route::post('/campaign/step3/store/new-uploads/{id}/{broadcaster}', 'Agency\CampaignsController@postNewUploads')->name('new.upload');
            Route::get('/camaigns/uploads/delete/{upload_id}/{id}', 'Agency\CampaignsController@deleteUpload')->name('agency.uploads.delete');
            Route::get('/review-uploads/{id}/{broadcaster}', 'Agency\CampaignsController@reviewUploads')->name('agency_campaign.review_uploads');
            Route::get('/campaign/step4/{id}/{broadcaster}/{start_date}/{end_date}', 'Agency\CampaignsController@getStep4')->name('agency_campaign.step4');
            Route::get('/campaigns/cart/store', 'Agency\CampaignsController@postPreselectedAdslot')->name('agency_campaign.cart');
            Route::get('/campaign/checkout/{id}', 'Agency\CampaignsController@checkout')->name('agency_campaign.checkout');
            Route::get('/cart/remove/{id}', 'Agency\CampaignsController@removeCart')->name('agency_cart.remove');
            Route::post('/campaign/submit/{id}', 'Agency\CampaignsController@postCampaign')->name('agency_submit.campaign');


            Route::get('/campaign-details/{id}', 'Agency\CampaignsController@getDetails')->name('agency.campaign.details');
            Route::get('/mpo-details/{id}', 'Agency\CampaignsController@mpoDetails')->name('agency.mpo.details');

            Route::get('/this/campaign-details/{campaign_id}', 'Agency\CampaignsController@filterByCampaignId');
            Route::get('/media-channel/{campaign_id}', 'Agency\CampaignsController@getMediaChannel');
            Route::get('/compliance-graph', 'Agency\CampaignsController@campaignBudgetGraph');
            Route::get('/compliance-graph/filter', 'Agency\CampaignsController@complianceGraph')->name('campaign_details.compliance');

            Route::post('/information-update/{campaign_id}', 'Agency\CampaignsController@updateAgencyCampaignInformation')->name('agency.campaign_information.update');
        });

        Route::get('/campaign-details/{user_id}', 'Agency\CampaignsController@filterByUser');

        /*
         * User Management
         */

        Route::get('/user/manage', 'Agency\UserManagementController@index')->name('agency.user_management');


        /**
         * Clients
         */
        Route::group(['prefix' => 'clients'], function () {
            Route::get('/list', 'ClientsController@clients')->name('clients.list');
            Route::get('/client/{client_id}', 'ClientsController@clientShow')->name('client.show');
            Route::get('/client/brand/{id}', 'ClientsController@getClientBrands')->name('client_brands');
            Route::get('/client/{client_id}/{user_id}', 'ClientsController@getCampaignData');
            Route::get('/client-month/{client_id}', 'ClientsController@filterByDate')->name('client.date');
            Route::get('/client-yearly/{client_id}', 'ClientsController@filterByYear')->name('client.year');
            Route::get('/client-brand/{id}/{client_id}', 'ClientsController@brandCampaign')->name('campaign.brand.client');
            Route::post('/update-client/{client_id}', 'ClientsController@updateClients')->name('agency.client.update');
        });

        Route::group(['prefix' => 'invoices'], function () {
            Route::get('/all', 'InvoiceController@all')->name('invoices.all');
            Route::get('/data', 'InvoiceController@getInvoiceDate');
            Route::get('/pending/data', 'InvoiceController@pendingData');
            Route::get('/pending', 'InvoiceController@pending')->name('invoices.pending');
            Route::post('/{invoice_id}/update', 'InvoiceController@approveInvoice')->name('invoices.update');
            Route::get('/details/{id}', 'InvoiceController@getInvoiceDetails')->name('invoice.details');
            Route::get('/export/pdf/{id}', 'InvoiceController@exportToPDF')->name('invoice.export');
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
    //      Route::get('/client-filter/campaign', 'Agency\ReportsController@filterCampaignClient')->name('filter.client');
        });

        /**
         * Media Planning
         */
        Route::group(['prefix' => 'media-plan'], function () {
            Route::get('/', 'MediaPlan\MediaPlanController@index')->name('agency.media_plans');
            Route::get('/dashboard/list', 'MediaPlan\MediaPlanController@dashboardMediaPlans');
            Route::get('/create', 'MediaPlan\MediaPlanController@criteriaForm')->name('agency.media_plan.criteria_form');
            Route::post('/create-plan', 'MediaPlan\MediaPlanController@generateRatingsPost')->name('agency.media_plan.suggestPlan');
            Route::get('/summary/{id}', 'MediaPlan\MediaPlanController@summary')->name('agency.media_plan.summary');
            Route::get('/approve/{id}', 'MediaPlan\MediaPlanController@approvePlan')->name('agency.media_plan.approve');
            Route::get('/decline/{id}', 'MediaPlan\MediaPlanController@declinePlan')->name('agency.media_plan.decline');
            Route::get('/customise/{id}', 'MediaPlan\MediaPlanController@getSuggestPlanById')->name('agency.media_plan.customize');
            Route::get('/vue/customise/{id}', 'MediaPlan\MediaPlanController@getSuggestPlanByIdVue');

            Route::post('/customise-filter', 'MediaPlan\MediaPlanController@setPlanSuggestionFilters')->name('agency.media_plan.customize-filter');

            Route::post('/select_plan', 'MediaPlan\MediaPlanController@SelectPlanPost');
            Route::get('/createplan/{id}', 'MediaPlan\MediaPlanController@CreatePlan')->name('agency.media_plan.create');
            Route::post('/finish_plan', 'MediaPlan\MediaPlanController@CompletePlan');
            Route::get('/export/{id}', 'MediaPlan\MediaPlanController@exportPlan')->name('agency.media_plan.export');
            Route::post('/store-programs', 'MediaPlan\MediaPlanController@storePrograms')->name('media_plan.program.store');
            Route::post('/store-volume-discount', 'MediaPlan\MediaPlanController@storeVolumeDiscount')->name('media_plan.volume_discount.store');
        });
        


        /**
         * User Management
         */

        Route::group(['prefix' => 'user'], function() {
            Route::get('/all', 'Agency\UserController@index')->name('agency.user.index');
            Route::get('/invite', 'Agency\UserController@inviteUser')->name('agency.user.invite');
            Route::get('/edit/{id}', 'Agency\UserController@editUser')->name('agency.user.edit');
            Route::post('/update/{id}', 'Agency\UserController@updateUser');
            Route::post('/invite/store', 'Agency\UserController@processInvite');
            Route::get('/data-table', 'Agency\UserController@getDatatable');
            Route::post('/resend/invitation', 'Agency\UserController@resendInvitation');
            Route::get('/status/update', 'Agency\UserController@updateStatus');
        });


}); 


Route::group(['domain' => 'stage.vantage'. substr($_SERVER['SERVER_NAME'], 7)], function() {

    Route::get('login', 'Auth\AuthController@getLogin')->name('login');
    Route::post('login', 'Auth\AuthController@postLogin')->name('post.login');
 
   Route::group(['prefix' => 'campaigns'], function() {
       Route::get('/all-campaigns/active', 'Agency\CampaignsController@index')->name('agency.campaign.all');
       Route::get('/all-campaign/data', 'Agency\CampaignsController@getData');
       Route::get('/all-clients', 'Agency\CampaignsController@allClient')->name('agency.campaign.create');
       Route::get('/all-client/data', 'Agency\CampaignsController@clientData');
       Route::get('/campaign/step1', 'Agency\CampaignsController@getStep1')->name('agency_campaign.step1');
       Route::post('/campaign/step1/store', 'Agency\CampaignsController@postStep1')->name('agency_campaign.store1');
       Route::get('/campaigns/step2/{id}', 'Agency\CampaignsController@getStep2')->name('agency_campaign.step2');
       Route::get('/campaign/step3/{id}', 'Agency\CampaignsController@getStep3')->name('agency_campaign.step3');
       Route::get('/campaign/step3/store/{id}', 'Agency\CampaignsController@postStep3')->name('agency_campaign.store3');
       Route::get('/campaign/step3/1/{id}', 'Agency\CampaignsController@getStep3_1')->name('agency_campaign.step3_1');
       Route::get('/campaign/step3/store/1/{id}', 'Agency\CampaignsController@postStep3_1')->name('agency_campaign.store3_1');
       Route::get('/campaign/step3/2/{id}', 'Agency\CampaignsController@getStep3_2')->name('agency_campaign.step3_2');
       Route::post('/campaign/step3/store/2/{id}/{broadcaster}', 'Agency\CampaignsController@postStep3_2')->name('agency_campaign.store3_2');
       Route::get('/campaign/step3/3/{id}/{broadcaster}', 'Agency\CampaignsController@getStep3_3')->name('agency_campaign.step3_3');
       Route::post('/campaign/step3/store/3/{id}/{broadcaster}', 'Agency\CampaignsController@postStep3_3')->name('agency_campaign.store3_3');
       Route::post('/campaign/step3/store/new-uploads/{id}/{broadcaster}', 'Agency\CampaignsController@postNewUploads')->name('new.upload');
       Route::get('/camaigns/uploads/delete/{upload_id}/{id}', 'Agency\CampaignsController@deleteUpload')->name('agency.uploads.delete');
       Route::get('/review-uploads/{id}/{broadcaster}', 'Agency\CampaignsController@reviewUploads')->name('agency_campaign.review_uploads');
       Route::get('/campaign/step4/{id}/{broadcaster}/{start_date}/{end_date}', 'Agency\CampaignsController@getStep4')->name('agency_campaign.step4');
       Route::get('/campaigns/cart/store', 'Agency\CampaignsController@postPreselectedAdslot')->name('agency_campaign.cart');
       Route::get('/campaign/checkout/{id}', 'Agency\CampaignsController@checkout')->name('agency_campaign.checkout');
       Route::get('/cart/remove/{id}', 'Agency\CampaignsController@removeCart')->name('agency_cart.remove');
       Route::post('/campaign/submit/{id}', 'Agency\CampaignsController@postCampaign')->name('agency_submit.campaign');


       Route::get('/campaign-details/{id}', 'Agency\CampaignsController@getDetails')->name('agency.campaign.details');
       Route::get('/mpo-details/{id}', 'Agency\CampaignsController@mpoDetails')->name('agency.mpo.details');

       Route::get('/this/campaign-details/{campaign_id}', 'Agency\CampaignsController@filterByCampaignId');
       Route::get('/media-channel/{campaign_id}', 'Agency\CampaignsController@getMediaChannel');
       Route::get('/compliance-graph', 'Agency\CampaignsController@campaignBudgetGraph');
       Route::get('/compliance-graph/filter', 'Agency\CampaignsController@complianceGraph')->name('campaign_details.compliance');

       Route::post('/information-update/{campaign_id}', 'Agency\CampaignsController@updateAgencyCampaignInformation')->name('agency.campaign_information.update');
   });

   Route::get('/campaign-details/{user_id}', 'Agency\CampaignsController@filterByUser');

   /*
    * User Management
    */

   Route::get('/user/manage', 'Agency\UserManagementController@index')->name('agency.user_management');


   /**
    * Clients
    */
   Route::group(['prefix' => 'clients'], function () {
       Route::get('/list', 'ClientsController@clients')->name('clients.list');
       Route::get('/client/{client_id}', 'ClientsController@clientShow')->name('client.show');
       Route::get('/client/brand/{id}', 'ClientsController@getClientBrands')->name('client_brands');
       Route::get('/client/{client_id}/{user_id}', 'ClientsController@getCampaignData');
       Route::get('/client-month/{client_id}', 'ClientsController@filterByDate')->name('client.date');
       Route::get('/client-yearly/{client_id}', 'ClientsController@filterByYear')->name('client.year');
       Route::get('/client-brand/{id}/{client_id}', 'ClientsController@brandCampaign')->name('campaign.brand.client');
       Route::post('/update-client/{client_id}', 'ClientsController@updateClients')->name('agency.client.update');
   });

   Route::group(['prefix' => 'invoices'], function () {
       Route::get('/all', 'InvoiceController@all')->name('invoices.all');
       Route::get('/data', 'InvoiceController@getInvoiceDate');
       Route::get('/pending/data', 'InvoiceController@pendingData');
       Route::get('/pending', 'InvoiceController@pending')->name('invoices.pending');
       Route::post('/{invoice_id}/update', 'InvoiceController@approveInvoice')->name('invoices.update');
       Route::get('/details/{id}', 'InvoiceController@getInvoiceDetails')->name('invoice.details');
       Route::get('/export/pdf/{id}', 'InvoiceController@exportToPDF')->name('invoice.export');
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
//      Route::get('/client-filter/campaign', 'Agency\ReportsController@filterCampaignClient')->name('filter.client');
   });

   /**
    * Media Planning
    */
   Route::group(['prefix' => 'media-plan'], function () {
       Route::get('/', 'MediaPlan\MediaPlanController@index')->name('agency.media_plans');
       Route::get('/dashboard/list', 'MediaPlan\MediaPlanController@dashboardMediaPlans');
       Route::get('/create', 'MediaPlan\MediaPlanController@criteriaForm')->name('agency.media_plan.criteria_form');
       Route::post('/create-plan', 'MediaPlan\MediaPlanController@generateRatingsPost')->name('agency.media_plan.suggestPlan');
       Route::get('/summary/{id}', 'MediaPlan\MediaPlanController@summary')->name('agency.media_plan.summary');
       Route::get('/approve/{id}', 'MediaPlan\MediaPlanController@approvePlan')->name('agency.media_plan.approve');
       Route::get('/decline/{id}', 'MediaPlan\MediaPlanController@declinePlan')->name('agency.media_plan.decline');
       Route::get('/customise/{id}', 'MediaPlan\MediaPlanController@getSuggestPlanById')->name('agency.media_plan.customize');
       Route::get('/vue/customise/{id}', 'MediaPlan\MediaPlanController@getSuggestPlanByIdVue');

       Route::post('/customise-filter', 'MediaPlan\MediaPlanController@setPlanSuggestionFilters')->name('agency.media_plan.customize-filter');

       Route::post('/select_plan', 'MediaPlan\MediaPlanController@SelectPlanPost');
       Route::get('/createplan/{id}', 'MediaPlan\MediaPlanController@CreatePlan')->name('agency.media_plan.create');
       Route::post('/finish_plan', 'MediaPlan\MediaPlanController@CompletePlan');
       Route::get('/export/{id}', 'MediaPlan\MediaPlanController@exportPlan')->name('agency.media_plan.export');
       Route::post('/store-programs', 'MediaPlan\MediaPlanController@storePrograms')->name('media_plan.program.store');
       Route::post('/store-volume-discount', 'MediaPlan\MediaPlanController@storeVolumeDiscount')->name('media_plan.volume_discount.store');
   });
   


   /**
    * User Management
    */

   Route::group(['prefix' => 'user'], function() {
       Route::get('/all', 'Agency\UserController@index')->name('agency.user.index');
       Route::get('/invite', 'Agency\UserController@inviteUser')->name('agency.user.invite');
       Route::get('/edit/{id}', 'Agency\UserController@editUser')->name('agency.user.edit');
       Route::post('/update/{id}', 'Agency\UserController@updateUser');
       Route::post('/invite/store', 'Agency\UserController@processInvite');
       Route::get('/data-table', 'Agency\UserController@getDatatable');
       Route::post('/resend/invitation', 'Agency\UserController@resendInvitation');
       Route::get('/status/update', 'Agency\UserController@updateStatus');
   });


}); 