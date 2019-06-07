<?php
$appRoutes = function() {

       Route::get('login', 'Auth\AuthController@getLogin')->name('login');
       Route::post('login', 'Auth\AuthController@postLogin')->name('post.login');
 
       Route::group(['namespace' => 'Agency','prefix' => 'campaigns'], function() {
       Route::get('/all-campaigns/active', 'CampaignsController@index')->name('agency.campaign.all');
       Route::get('/all-campaign/data', 'CampaignsController@getData');
       Route::get('/all-clients', 'CampaignsController@allClient')->name('agency.campaign.create');
       Route::get('/all-client/data', 'CampaignsController@clientData');
       Route::get('/campaign/step1', 'CampaignsController@getStep1')->name('agency_campaign.step1');
       Route::post('/campaign/step1/store', 'CampaignsController@postStep1')->name('agency_campaign.store1');
       Route::get('/campaigns/step2/{id}', 'CampaignsController@getStep2')->name('agency_campaign.step2');
       Route::get('/campaign/step3/{id}', 'CampaignsController@getStep3')->name('agency_campaign.step3');
       Route::get('/campaign/step3/store/{id}', 'CampaignsController@postStep3')->name('agency_campaign.store3');
       Route::get('/campaign/step3/1/{id}', 'CampaignsController@getStep3_1')->name('agency_campaign.step3_1');
       Route::get('/campaign/step3/store/1/{id}', 'CampaignsController@postStep3_1')->name('agency_campaign.store3_1');
       Route::get('/campaign/step3/2/{id}', 'CampaignsController@getStep3_2')->name('agency_campaign.step3_2');
       Route::post('/campaign/step3/store/2/{id}/{broadcaster}', 'CampaignsController@postStep3_2')->name('agency_campaign.store3_2');
       Route::get('/campaign/step3/3/{id}/{broadcaster}', 'CampaignsController@getStep3_3')->name('agency_campaign.step3_3');
       Route::post('/campaign/step3/store/3/{id}/{broadcaster}', 'CampaignsController@postStep3_3')->name('agency_campaign.store3_3');
       Route::post('/campaign/step3/store/new-uploads/{id}/{broadcaster}', 'CampaignsController@postNewUploads')->name('new.upload');
       Route::get('/camaigns/uploads/delete/{upload_id}/{id}', 'CampaignsController@deleteUpload')->name('agency.uploads.delete');
       Route::get('/review-uploads/{id}/{broadcaster}', 'CampaignsController@reviewUploads')->name('agency_campaign.review_uploads');
       Route::get('/campaign/step4/{id}/{broadcaster}/{start_date}/{end_date}', 'CampaignsController@getStep4')->name('agency_campaign.step4');
       Route::get('/campaigns/cart/store', 'CampaignsController@postPreselectedAdslot')->name('agency_campaign.cart');
       Route::get('/campaign/checkout/{id}', 'CampaignsController@checkout')->name('agency_campaign.checkout');
       Route::get('/cart/remove/{id}', 'CampaignsController@removeCart')->name('agency_cart.remove');
       Route::post('/campaign/submit/{id}', 'CampaignsController@postCampaign')->name('agency_submit.campaign');


       Route::get('/campaign-details/{id}', 'CampaignsController@getDetails')->name('agency.campaign.details');
       Route::get('/mpo-details/{id}', 'CampaignsController@mpoDetails')->name('agency.mpo.details');

       Route::get('/this/campaign-details/{campaign_id}', 'CampaignsController@filterByCampaignId');
       Route::get('/media-channel/{campaign_id}', 'CampaignsController@getMediaChannel');
       Route::get('/compliance-graph', 'CampaignsController@campaignBudgetGraph');
       Route::get('/compliance-graph/filter', 'CampaignsController@complianceGraph')->name('campaign_details.compliance');

       Route::post('/information-update/{campaign_id}', 'CampaignsController@updateAgencyCampaignInformation')->name('agency.campaign_information.update');
   });

   Route::get('/campaign-details/{user_id}', 'CampaignsController@filterByUser');

   /*
    * User Management
    */

   Route::get('/user/manage', 'UserManagementController@index')->name('agency.user_management');


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

   Route::group(['namespace' => 'Agency','prefix' => 'wallets'], function(){
       Route::get('/wallet/credit', 'WalletsController@create')->name('agency_wallet.create');
       Route::get('/wallet-statement', 'WalletsController@index')->name('agency_wallet.statement');
       Route::post('/wallet/amount', 'WalletsController@getAmount')->name('wallet.amount');
       Route::get('/wallet/amount/pay', 'WalletsController@getPay')->name('amount.pay');
       Route::post('/pay', 'WalletsController@pay')->name('pay');
       Route::get('/get-wallet/data', 'WalletsController@getData');
   });

   Route::group(['namespace' => 'Agency','prefix' => 'reports'], function(){
       Route::get('/', 'ReportsController@index')->name('reports.index');
       Route::get('/campaign/all-data', 'ReportsController@getCampaign');
       Route::get('/revenue/all-data', 'ReportsController@getRevenue');
   //      Route::get('/client-filter/campaign', 'Agency\ReportsController@filterCampaignClient')->name('filter.client');
   });

   /**
    * Media Planning
    */
   Route::group(['namespace' => 'MediaPlan','prefix' => 'media-plan'], function () {
       Route::get('/', 'MediaPlanController@index')->name('agency.media_plans');
       Route::get('/dashboard/list', 'MediaPlanController@dashboardMediaPlans');
       Route::get('/create', 'MediaPlanController@criteriaForm')->name('agency.media_plan.criteria_form');
       Route::post('/create-plan', 'MediaPlanController@generateRatingsPost')->name('agency.media_plan.suggestPlan');
       Route::get('/summary/{id}', 'MediaPlanController@summary')->name('agency.media_plan.summary');
       Route::get('/approve/{id}', 'MediaPlanController@approvePlan')->name('agency.media_plan.approve');
       Route::get('/decline/{id}', 'MediaPlanController@declinePlan')->name('agency.media_plan.decline');
       Route::get('/customise/{id}', 'MediaPlanController@getSuggestPlanById')->name('agency.media_plan.customize');
       Route::get('/vue/customise/{id}', 'MediaPlanController@getSuggestPlanByIdVue');

       Route::post('/customise-filter', 'MediaPlanController@setPlanSuggestionFilters')->name('agency.media_plan.customize-filter');

       Route::post('/select_plan', 'MediaPlanController@SelectPlanPost');
       Route::get('/createplan/{id}', 'MediaPlanController@CreatePlan')->name('agency.media_plan.create');
       Route::post('/finish_plan', 'MediaPlanController@CompletePlan');
       Route::get('/export/{id}', 'MediaPlanController@exportPlan')->name('agency.media_plan.export');
       Route::post('/store-programs', 'MediaPlanController@storePrograms')->name('media_plan.program.store');
       Route::post('/store-volume-discount', 'MediaPlanController@storeVolumeDiscount')->name('media_plan.volume_discount.store');
   });
   


   /**
    * User Management
    */

   Route::group(['namespace' => 'Agency','prefix' => 'user'], function() {
       Route::get('/all', 'UserController@index')->name('agency.user.index');
       Route::get('/invite', 'UserController@inviteUser')->name('agency.user.invite');
       Route::get('/edit/{id}', 'UserController@editUser')->name('agency.user.edit');
       Route::post('/update/{id}', 'UserController@updateUser');
       Route::post('/invite/store', 'UserController@processInvite');
       Route::get('/data-table', 'UserController@getDatatable');
       Route::post('/resend/invitation', 'UserController@resendInvitation');
       Route::get('/status/update', 'UserController@updateStatus');
   });


};

Route::group(['domain' => 'vantage.'. URL::to('/')], $appRoutes); 
Route::group(['domain' => 'stage.vantage.'. URL::to('/')], $appRoutes ); 





