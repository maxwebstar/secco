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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/test', 'TestController@index')->name('test');

Route::group(['middleware' => ['role:admin|ad_ops|accounting|account_manager|sales']], function() {
    Route::get('/admin/test', ['uses' => 'Admin\TestController@index', 'as' => 'admin.test']);
    Route::get('/admin/test/mail', ['uses' => 'Admin\TestController@mail', 'as' => 'admin.test.mail']);
    Route::get('/admin/test/lt', ['uses' => 'Admin\TestController@lt', 'as' => 'admin.test.lt']);
    Route::get('/admin/test/ef', ['uses' => 'Admin\TestController@ef', 'as' => 'admin.test.ef']);
    Route::get('/admin/test/word', ['uses' => 'Admin\TestController@word', 'as' => 'admin.test.word']);
    Route::get('/admin/test/pipe', ['uses' => 'Admin\TestController@pipe', 'as' => 'admin.test.pipe']);
    Route::get('/admin/test/docusign', ['uses' => 'Admin\TestController@docusign', 'as' => 'admin.test.docusign']);
    Route::get('/admin/test/docusign-doc', ['uses' => 'Admin\TestController@docusignDoc', 'as' => 'admin.test.docusign.doc']);
    Route::get('/admin/test/pdf', ['uses' => 'Admin\TestController@pdf', 'as' => 'admin.test.pdf']);
    Route::get('/admin/test/docusign-info', ['uses' => 'Admin\TestController@docusignInfo', 'as' => 'admin.test.docusign.info']);
    Route::get('/admin/test/create-google-folder', ['uses' => 'Admin\TestController@createGoogleFolder', 'as' => 'admin.test.create.google.folder']);
});


/* admin ad_ops accounting account_manager sales */
Route::group(['middleware' => ['role:admin|ad_ops|accounting|account_manager|sales']], function() {
    Route::get('/admin/dashboard', ['uses' => 'Admin\DashboardController@index', 'as' => 'admin.dashboard']);
    Route::match(['get', 'post'], '/admin/profile', ['uses' => 'Admin\UserController@profile', 'as' => 'admin.profile']);

    Route::get('/admin/advertiser/index', ['uses' => 'Admin\AdvertiserController@index', 'as' => 'admin.advertiser']);
    Route::get('/admin/advertiser/add/{deal_id?}', ['uses' => 'Admin\AdvertiserController@add', 'as' => 'admin.advertiser.add'])->where(['deal_id' => '[0-9]+']);
    Route::get('/admin/advertiser/edit/{id}', ['uses' => 'Admin\AdvertiserController@edit', 'as' => 'admin.advertiser.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/advertiser/save-add', ['uses' => 'Admin\AdvertiserController@saveAdd', 'as' => 'admin.advertiser.save.add']);
    Route::post('/admin/advertiser/save-edit', ['uses' => 'Admin\AdvertiserController@saveEdit', 'as' => 'admin.advertiser.save.edit']);
    Route::post('/admin/advertiser/ajax-get', ['uses' => 'Admin\AdvertiserController@ajaxGet', 'as' => 'admin.advertiser.ajax.get']);
    Route::get('/admin/advertiser/profile/{id?}', ['uses' => 'Admin\AdvertiserController@profile', 'as' => 'admin.advertiser.profile'])->where(['id' => '[0-9]+']);
    Route::get('/admin/advertiser/missing', ['uses' => 'Admin\AdvertiserController@missing', 'as' => 'admin.advertiser.missing']);
    Route::post('/admin/advertiser/ajax-get-missing', ['uses' => 'Admin\AdvertiserController@ajaxGetMissing', 'as' => 'admin.advertiser.ajax.get.missing']);
    Route::get('/admin/advertiser/view-missing/{id}', ['uses' => 'Admin\AdvertiserController@viewMissing', 'as' => 'admin.advertiser.view.missing'])->where(['id' => '[0-9]+']);
    Route::get('/admin/advertiser/add-missing/{id}', ['uses' => 'Admin\AdvertiserController@addMissing', 'as' => 'admin.advertiser.add.missing'])->where(['id' => '[0-9]+']);
    Route::get('/admin/advertiser/ignore-missing/{id}', ['uses' => 'Admin\AdvertiserController@ignoreMissing', 'as' => 'admin.advertiser.ignore.missing'])->where(['id' => '[0-9]+']);
    Route::post('/admin/advertiser/save-add-missing', ['uses' => 'Admin\AdvertiserController@saveAddMissing', 'as' => 'admin.advertiser.save.add.missing']);

    Route::get('/admin/io/add/{deal_id?}', ['uses' => 'Admin\IOController@add', 'as' => 'admin.io.add'])->where(['deal_id' => '[0-9]+']);
    Route::post('/admin/io/save-add', ['uses' => 'Admin\IOController@saveAdd', 'as' => 'admin.io.save.add']);
    Route::get('/admin/io', ['uses' => 'Admin\IOController@index', 'as' => 'admin.io.index']);
    Route::get('/admin/io/individual', ['uses' => 'Admin\IOController@individual', 'as' => 'admin.io.individual']);
    Route::post('/admin/io/save-individual', ['uses' => 'Admin\IOController@saveIndividual', 'as' => 'admin.io.save.individual']);
    Route::post('/admin/io/ajax-get', ['uses' => 'Admin\IOController@ajaxGet', 'as' => 'admin.io.ajax.get']);
    Route::get('/admin/io/view/{id}', ['uses' => 'Admin\IOController@view', 'as' => 'admin.io.view'])->where(['id' => '[0-9]+']);
    Route::get('/admin/io/approve/{id}', ['uses' => 'Admin\IOController@approve', 'as' => 'admin.io.approve'])->where(['id' => '[0-9]+']);
    Route::post('/admin/io/approve', ['uses' => 'Admin\IOController@saveApprove', 'as' => 'admin.io.save.approve']);
    Route::get('/admin/io/decline/{id}', ['uses' => 'Admin\IOController@decline', 'as' => 'admin.io.decline'])->where(['id' => '[0-9]+']);
    Route::post('/admin/io/check-api-docusign', ['uses' => 'Admin\IOController@checkApiDocusign', 'as' => 'admin.io.check.api.docusign']);
    Route::post('/admin/io/upload', ['uses' => 'Admin\IOController@saveUpload', 'as' => 'admin.io.save.upload']);
    Route::post('/admin/io/delete-upload', ['uses' => 'Admin\IOController@deleteUpload', 'as' => 'admin.io.delete.upload']);

    Route::get('/admin/offer/add/{network?}', ['uses' => 'Admin\OfferController@add', 'as' => 'admin.offer.add'])->where(['network' => '[a-z]+']);
    Route::post('/admin/offer/save-add', ['uses' => 'Admin\OfferController@saveAdd', 'as' => 'admin.offer.save.add']);
    Route::get('/admin/offer/edit-new/{id}/{network?}', ['uses' => 'Admin\OfferController@editNew', 'as' => 'admin.offer.edit.new'])->where(['id' => '[0-9]+', 'network' => '[a-z]+']);

    Route::post('/admin/offer/save-edit-new', ['uses' => 'Admin\OfferController@saveEditNew', 'as' => 'admin.offer.save.edit.new']);
    Route::get('/admin/offer/index', ['uses' => 'Admin\OfferController@index', 'as' => 'admin.offer.index']);
    Route::post('/admin/offer/ajax-get', ['uses' => 'Admin\OfferController@ajaxGet', 'as' => 'admin.offer.ajax.get']);
    Route::get('/admin/offer/view/{id}', ['uses' => 'Admin\OfferController@view', 'as' => 'admin.offer.view'])->where(['id' => '[0-9]+']);
    Route::get('/admin/offer/decline/{id}', ['uses' => 'Admin\OfferController@decline', 'as' => 'admin.offer.decline'])->where(['id' => '[0-9]+']);
    Route::post('/admin/offer/decline', ['uses' => 'Admin\OfferController@saveDecline', 'as' => 'admin.offer.save.decline']);
    Route::get('/admin/offer/approve/{id}', ['uses' => 'Admin\OfferController@approve', 'as' => 'admin.offer.approve'])->where(['id' => '[0-9]+']);
    Route::get('/admin/offer/profile/{id?}', ['uses' => 'Admin\OfferController@profile', 'as' => 'admin.offer.profile'])->where(['id' => '[0-9]+']);
    Route::get('/admin/offer/edit/{id?}', ['uses' => 'Admin\OfferController@edit', 'as' => 'admin.offer.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/offer/save-edit', ['uses' => 'Admin\OfferController@saveEdit', 'as' => 'admin.offer.save.edit']);

    Route::post('/admin/ajax/search-advertiser', ['uses' => 'Admin\AjaxController@searchAdvertiser', 'as' => 'admin.ajax.search.advertiser']);
    Route::post('/admin/ajax/search-offer', ['uses' => 'Admin\AjaxController@searchOffer', 'as' => 'admin.ajax.search.offer']);
    Route::post('/admin/ajax/search-country', ['uses' => 'Admin\AjaxController@searchCountry', 'as' => 'admin.ajax.search.country']);
    Route::post('/admin/ajax/search-affiliate-by-offer', ['uses' => 'Admin\AjaxController@searchAffiliateByOffer', 'as' => 'admin.ajax.search.affiliate.by.offer']);
    Route::post('/admin/ajax/get-advertiser', ['uses' => 'Admin\AjaxController@getAdvertiser', 'as' => 'admin.ajax.get.advertiser']);
    Route::post('/admin/ajax/get-offer', ['uses' => 'Admin\AjaxController@getOffer', 'as' => 'admin.ajax.get.offer']);
    Route::post('/admin/ajax/save-io-gov-date', ['uses' => 'Admin\AjaxController@saveIOGovDate', 'as' => 'admin.ajax.save.io.gov.date']);
    Route::post('/admin/ajax/get-creative-by-request', ['uses' => 'Admin\AjaxController@getCreativeByRequest', 'as' => 'admin.ajax.get.creative.by.request']);
    Route::post('/admin/ajax/search-creative-by-offer', ['uses' => 'Admin\AjaxController@searchCreativeByOffer', 'as' => 'admin.ajax.search.creative.by.offer']);
    Route::post('/admin/ajax/search-creative-by-offer-without-network', ['uses' => 'Admin\AjaxController@searchCreativeByOfferWithoutNetwork', 'as' => 'admin.ajax.search.creative.by.offer.without.network']);
    Route::post('/admin/ajax/get-tracking-by-offer', ['uses' => 'Admin\AjaxController@getTrackingByOffer', 'as' => 'admin.ajax.get.tracking.by.offer']);

    Route::get('/admin/pipedrive', ['uses' => 'Admin\PipeDriveController@index', 'as' => 'admin.pipedrive.index']);
    Route::post('/admin/pipedrive/ajax-get', ['uses' => 'Admin\PipeDriveController@ajaxGet', 'as' => 'admin.pipedrive.ajax.get']);
    Route::get('/admin/pipedrive/delete/{id}', ['uses' => 'Admin\PipeDriveController@delete', 'as' => 'admin.pipedrive.delete'])->where(['id' => '[0-9]+']);

    Route::get('/admin/request/cap/index', ['uses' => 'Admin\Request\CapController@index', 'as' => 'admin.request.cap.index']);
    Route::get('/admin/request/cap/add', ['uses' => 'Admin\Request\CapController@add', 'as' => 'admin.request.cap.add']);
    Route::get('/admin/request/cap/edit/{id}', ['uses' => 'Admin\Request\CapController@edit', 'as' => 'admin.request.cap.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/request/cap/add', ['uses' => 'Admin\Request\CapController@saveAdd', 'as' => 'admin.request.cap.save.add']);
    Route::post('/admin/request/cap/edit', ['uses' => 'Admin\Request\CapController@saveEdit', 'as' => 'admin.request.cap.save.edit']);
    Route::post('/admin/request/cap/ajax-get', ['uses' => 'Admin\Request\CapController@ajaxGet', 'as' => 'admin.request.cap.ajax.get']);
    Route::get('/admin/request/cap/view/{id}', ['uses' => 'Admin\Request\CapController@view', 'as' => 'admin.request.cap.view'])->where(['id' => '[0-9]+']);
    Route::get('/admin/request/cap/decline/{id}', ['uses' => 'Admin\Request\CapController@decline', 'as' => 'admin.request.cap.decline'])->where(['id' => '[0-9]+']);
    Route::post('/admin/request/cap/decline', ['uses' => 'Admin\Request\CapController@saveDecline', 'as' => 'admin.request.cap.save.decline']);
    Route::get('/admin/request/cap/approve/{id}', ['uses' => 'Admin\Request\CapController@approve', 'as' => 'admin.request.cap.approve'])->where(['id' => '[0-9]+']);

    Route::get('/admin/request/status/index', ['uses' => 'Admin\Request\StatusController@index', 'as' => 'admin.request.status.index']);
    Route::get('/admin/request/status/add', ['uses' => 'Admin\Request\StatusController@add', 'as' => 'admin.request.status.add']);
    Route::get('/admin/request/status/edit/{id}', ['uses' => 'Admin\Request\StatusController@edit', 'as' => 'admin.request.status.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/request/status/add', ['uses' => 'Admin\Request\StatusController@saveAdd', 'as' => 'admin.request.status.save.add']);
    Route::post('/admin/request/status/edit', ['uses' => 'Admin\Request\StatusController@saveEdit', 'as' => 'admin.request.status.save.edit']);
    Route::post('/admin/request/status/ajax-get', ['uses' => 'Admin\Request\StatusController@ajaxGet', 'as' => 'admin.request.status.ajax.get']);
    Route::get('/admin/request/status/view/{id}', ['uses' => 'Admin\Request\StatusController@view', 'as' => 'admin.request.status.view'])->where(['id' => '[0-9]+']);
    Route::get('/admin/request/status/decline/{id}', ['uses' => 'Admin\Request\StatusController@decline', 'as' => 'admin.request.status.decline'])->where(['id' => '[0-9]+']);
    Route::post('/admin/request/status/decline', ['uses' => 'Admin\Request\StatusController@saveDecline', 'as' => 'admin.request.status.save.decline']);
    Route::get('/admin/request/status/approve/{id}', ['uses' => 'Admin\Request\StatusController@approve', 'as' => 'admin.request.status.approve'])->where(['id' => '[0-9]+']);

    Route::get('/admin/request/price/index', ['uses' => 'Admin\Request\PriceController@index', 'as' => 'admin.request.price.index']);
    Route::get('/admin/request/price/add', ['uses' => 'Admin\Request\PriceController@add', 'as' => 'admin.request.price.add']);
    Route::get('/admin/request/price/edit/{id}', ['uses' => 'Admin\Request\PriceController@edit', 'as' => 'admin.request.price.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/request/price/add', ['uses' => 'Admin\Request\PriceController@saveAdd', 'as' => 'admin.request.price.save.add']);
    Route::post('/admin/request/price/edit', ['uses' => 'Admin\Request\PriceController@saveEdit', 'as' => 'admin.request.price.save.edit']);
    Route::post('/admin/request/price/ajax-get', ['uses' => 'Admin\Request\PriceController@ajaxGet', 'as' => 'admin.request.price.ajax.get']);
    Route::get('/admin/request/price/view/{id}', ['uses' => 'Admin\Request\PriceController@view', 'as' => 'admin.request.price.view'])->where(['id' => '[0-9]+']);
    Route::get('/admin/request/price/decline/{id}', ['uses' => 'Admin\Request\PriceController@decline', 'as' => 'admin.request.price.decline'])->where(['id' => '[0-9]+']);
    Route::post('/admin/request/price/decline', ['uses' => 'Admin\Request\PriceController@saveDecline', 'as' => 'admin.request.price.save.decline']);
    Route::get('/admin/request/price/approve/{id}', ['uses' => 'Admin\Request\PriceController@approve', 'as' => 'admin.request.price.approve'])->where(['id' => '[0-9]+']);

    Route::get('/admin/request/creative/index', ['uses' => 'Admin\Request\CreativeController@index', 'as' => 'admin.request.creative.index']);
    Route::get('/admin/request/creative/add', ['uses' => 'Admin\Request\CreativeController@add', 'as' => 'admin.request.creative.add']);
    Route::get('/admin/request/creative/edit/{id}', ['uses' => 'Admin\Request\CreativeController@edit', 'as' => 'admin.request.creative.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/request/creative/add', ['uses' => 'Admin\Request\CreativeController@saveAdd', 'as' => 'admin.request.creative.save.add']);
    Route::post('/admin/request/creative/edit', ['uses' => 'Admin\Request\CreativeController@saveEdit', 'as' => 'admin.request.creative.save.edit']);
    Route::post('/admin/request/creative/ajax-get', ['uses' => 'Admin\Request\CreativeController@ajaxGet', 'as' => 'admin.request.creative.ajax.get']);
    Route::get('/admin/request/creative/view/{id}', ['uses' => 'Admin\Request\CreativeController@view', 'as' => 'admin.request.creative.view'])->where(['id' => '[0-9]+']);
    Route::get('/admin/request/creative/decline/{id}', ['uses' => 'Admin\Request\CreativeController@decline', 'as' => 'admin.request.creative.decline'])->where(['id' => '[0-9]+']);
    Route::post('/admin/request/creative/decline', ['uses' => 'Admin\Request\CreativeController@saveDecline', 'as' => 'admin.request.creative.save.decline']);
    Route::get('/admin/request/creative/approve/{id}', ['uses' => 'Admin\Request\CreativeController@approve', 'as' => 'admin.request.creative.approve'])->where(['id' => '[0-9]+']);
    Route::get('/admin/request/creative/missing', ['uses' => 'Admin\Request\CreativeController@missing', 'as' => 'admin.request.creative.missing']);
    Route::get('/admin/request/creative/view-missing/{id}', ['uses' => 'Admin\Request\CreativeController@viewMissing', 'as' => 'admin.request.creative.view.missing'])->where(['id' => '[0-9]+']);
    Route::get('/admin/request/creative/ignore-missing/{id}', ['uses' => 'Admin\Request\CreativeController@ignoreMissing', 'as' => 'admin.request.creative.ignore.missing'])->where(['id' => '[0-9]+']);
    Route::get('/admin/request/creative/add-missing/{id}', ['uses' => 'Admin\Request\CreativeController@addMissing', 'as' => 'admin.request.creative.add.missing'])->where(['id' => '[0-9]+']);
    Route::get('/admin/request/creative/attach-missing/{id}', ['uses' => 'Admin\Request\CreativeController@attachMissing', 'as' => 'admin.request.creative.attach.missing'])->where(['id' => '[0-9]+']);
    Route::post('/admin/request/creative/ajax-get-missing', ['uses' => 'Admin\Request\CreativeController@ajaxGetMissing', 'as' => 'admin.request.creative.ajax.get.missing']);
    Route::post('/admin/request/creative/save-attach-missing', ['uses' => 'Admin\Request\CreativeController@saveAttachMissing', 'as' => 'admin.request.creative.save.attach.missing']);

    Route::get('/admin/request/massadjustment/index', ['uses' => 'Admin\Request\MassAdjustmentController@index', 'as' => 'admin.request.mass.adjustment.index']);
    Route::get('/admin/request/massadjustment/add', ['uses' => 'Admin\Request\MassAdjustmentController@add', 'as' => 'admin.request.mass.adjustment.add']);
    Route::get('/admin/request/massadjustment/edit/{id}', ['uses' => 'Admin\Request\MassAdjustmentController@edit', 'as' => 'admin.request.mass.adjustment.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/request/massadjustment/add', ['uses' => 'Admin\Request\MassAdjustmentController@saveAdd', 'as' => 'admin.request.mass.adjustment.save.add']);
    Route::post('/admin/request/massadjustment/edit', ['uses' => 'Admin\Request\MassAdjustmentController@saveEdit', 'as' => 'admin.request.mass.adjustment.save.edit']);
    Route::post('/admin/request/massadjustment/ajax-get', ['uses' => 'Admin\Request\MassAdjustmentController@ajaxGet', 'as' => 'admin.request.mass.adjustment.ajax.get']);
    Route::get('/admin/request/massadjustment/view/{id}', ['uses' => 'Admin\Request\MassAdjustmentController@view', 'as' => 'admin.request.mass.adjustment.view'])->where(['id' => '[0-9]+']);
    Route::get('/admin/request/massadjustment/decline/{id}', ['uses' => 'Admin\Request\MassAdjustmentController@decline', 'as' => 'admin.request.mass.adjustment.decline'])->where(['id' => '[0-9]+']);
    Route::post('/admin/request/massadjustment/decline', ['uses' => 'Admin\Request\MassAdjustmentController@saveDecline', 'as' => 'admin.request.mass.adjustment.save.decline']);
    Route::get('/admin/request/massadjustment/approve/{id}', ['uses' => 'Admin\Request\MassAdjustmentController@approve', 'as' => 'admin.request.mass.adjustment.approve'])->where(['id' => '[0-9]+']);

    Route::get('/admin/creditcap/index', ['uses' => 'Admin\CreditCapController@index', 'as' => 'admin.credit.cap.index']);
    Route::post('/admin/creditcap/ajax-get', ['uses' => 'Admin\CreditCapController@ajaxGet', 'as' => 'admin.credit.cap.ajax.get']);
    Route::get('/admin/creditcap/report', ['uses' => 'Admin\CreditCapController@report', 'as' => 'admin.credit.cap.report']);
    Route::post('/admin/creditcap/ajax-get-report', ['uses' => 'Admin\CreditCapController@ajaxGetReport', 'as' => 'admin.credit.cap.ajax.get.report']);
    Route::get('/admin/creditcap/report-month', ['uses' => 'Admin\CreditCapController@reportMonth', 'as' => 'admin.credit.cap.report.month']);
    Route::post('/admin/creditcap/ajax-get-report-month', ['uses' => 'Admin\CreditCapController@ajaxGetReportMonth', 'as' => 'admin.credit.cap.ajax.get.report.month']);
    Route::post('/admin/creditcap/ajax-check', ['uses' => 'Admin\CreditCapController@ajaxCheck', 'as' => 'admin.credit.cap.ajax.check']);

    Route::get('/admin/qb/customer/index', ['uses' => 'Admin\QB\CustomerController@index', 'as' => 'admin.qb.customer.index']);
    Route::post('/admin/qb/customer/ajax-get', ['uses' => 'Admin\QB\CustomerController@ajaxGet', 'as' => 'admin.qb.customer.ajax.get']);
    Route::get('/admin/qb/customer/attache/{id}', ['uses' => 'Admin\QB\CustomerController@attache', 'as' => 'admin.qb.customer.attache'])->where(['id' => '[0-9]+']);
    Route::post('/admin/qb/customer/save-attache', ['uses' => 'Admin\QB\CustomerController@saveAttache', 'as' => 'admin.qb.customer.save.attache']);
    Route::get('/admin/qb/customer/view/{id}', ['uses' => 'Admin\QB\CustomerController@view', 'as' => 'admin.qb.customer.view'])->where(['id' => '[0-9]+']);


    Route::get('/admin/userparamemail/edit', ['uses' => 'Admin\UserParamEmailController@edit', 'as' => 'admin.userparamemail.edit']);
    Route::post('/admin/userparamemail/save', ['uses' => 'Admin\UserParamEmailController@save', 'as' => 'admin.userparamemail.save']);

    Route::get('/admin/slickpuller/index', ['uses' => 'Admin\SlickPullerController@index', 'as' => 'admin.slick.puller.index']);
    Route::post('/admin/slickpuller/ajax-get-data-lt', ['uses' => 'Admin\SlickPullerController@ajaxGetDataLT', 'as' => 'admin.slick.puller.ajax.get.data.lt']);
    Route::post('/admin/slickpuller/ajax-get-data-ef', ['uses' => 'Admin\SlickPullerController@ajaxGetDataEF', 'as' => 'admin.slick.puller.ajax.get.data.ef']);
    Route::post('/admin/slickpuller/ajax-get-tracking-url', ['uses' => 'Admin\SlickPullerController@ajaxGetTrackingUrl', 'as' => 'admin.slick.puller.ajax.get.tracking.url']);

    Route::get('/admin/prepay/index', ['uses' => 'Admin\PrePayController@index', 'as' => 'admin.prepay.index']);
    Route::post('/admin/prepay/ajax-get-data', ['uses' => 'Admin\PrePayController@ajaxGet', 'as' => 'admin.prepay.ajax.get']);
    Route::get('/admin/prepay/csv-export', ['uses' => 'Admin\PrePayController@csvExport', 'as' => 'admin.prepay.csv.export']);

    Route::get('/admin/access/index', ['uses' => 'Admin\AccessController@index', 'as' => 'admin.access.index']);
    Route::get('/admin/access/add', ['uses' => 'Admin\AccessController@add', 'as' => 'admin.access.add']);
    Route::get('/admin/access/edit/{id}', ['uses' => 'Admin\AccessController@edit', 'as' => 'admin.access.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/access/save', ['uses' => 'Admin\AccessController@save', 'as' => 'admin.access.save']);
    Route::get('/admin/access/login', ['uses' => 'Admin\AccessController@login', 'as' => 'admin.access.login']);
    Route::post('/admin/access/save-check', ['uses' => 'Admin\AccessController@loginCheck', 'as' => 'admin.access.login.check']);
});

/* admin ad_ops accounting account_manager */
Route::group(['middleware' => ['role:admin|ad_ops|accounting|account_manager']], function() {
    Route::get('/admin/user', ['uses' => 'Admin\UserController@index', 'as' => 'admin.user']);

    Route::get('/admin/domain', ['uses' => 'Admin\DomainController@index', 'as' => 'admin.domain']);
    Route::get('/admin/domain/add', ['uses' => 'Admin\DomainController@add', 'as' => 'admin.domain.add']);
    Route::get('/admin/domain/edit/{id}', ['uses' => 'Admin\DomainController@edit', 'as' => 'admin.domain.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/domain/save', ['uses' => 'Admin\DomainController@save', 'as' => 'admin.domain.save']);

    Route::get('/admin/request/statistic/index', ['uses' => 'Admin\Request\StatisticController@index', 'as' => 'admin.request.statistic.index']);
    Route::post('/admin/request/statistic/ajax-get', ['uses' => 'Admin\Request\StatisticController@ajaxGet', 'as' => 'admin.request.statistic.ajax.get']);
    Route::post('/admin/request/statistic/ajax-save', ['uses' => 'Admin\Request\StatisticController@ajaxSave', 'as' => 'admin.request.statistic.ajax.save']);
    Route::post('/admin/request/statistic/ajax-save-notification', ['uses' => 'Admin\Request\StatisticController@ajaxSaveNotification', 'as' => 'admin.request.statistic.ajax.save.notification']);
    Route::post('/admin/request/statistic/ajax-save-send', ['uses' => 'Admin\Request\StatisticController@ajaxSaveSend', 'as' => 'admin.request.statistic.ajax.save.send']);

    Route::get('/admin/fxrate/index', ['uses' => 'Admin\FXRateController@index', 'as' => 'admin.fxrate.index']);
    Route::post('/admin/fxrate/ajax-get-campaign', ['uses' => 'Admin\FXRateController@ajaxGetCampaign', 'as' => 'admin.fxrate.ajax.get.campaign']);
    Route::post('/admin/fxrate/ajax-update-price', ['uses' => 'Admin\FXRateController@ajaxUpdatePrice', 'as' => 'admin.fxrate.ajax.update.price']);
});
/* admin */
Route::group(['middleware' => ['role:admin']], function() {
    Route::match(['get', 'post'], '/admin/user/add', ['uses' => 'Admin\UserController@add', 'as' => 'admin.user.add']);
    Route::get('/admin/user/view/{id}', ['uses' => 'Admin\UserController@view', 'as' => 'admin.user.view'])->where(['id' => '[0-9]+']);
    Route::post('/admin/user/update', ['uses' => 'Admin\UserController@update', 'as' => 'admin.user.update']);
    Route::get('/admin/user/approve/{id}', ['uses' => 'Admin\UserController@approve', 'as' => 'admin.user.approve'])->where(['id' => '[0-9]+']);
    Route::get('/admin/user/reject/{id}', ['uses' => 'Admin\UserController@reject', 'as' => 'admin.user.reject'])->where(['id' => '[0-9]+']);
    Route::get('/admin/user/delete/{id}', ['uses' => 'Admin\UserController@delete', 'as' => 'admin.user.delete'])->where(['id' => '[0-9]+']);

    Route::match(['get', 'post'], '/admin/permission/manage', ['uses' => 'Admin\PermissionController@manage', 'as' => 'admin.permission.manage']);
    Route::post('/admin/permission/manage-save', ['uses' => 'Admin\PermissionController@manageSave', 'as' => 'admin.permission.manage.save']);

    Route::get('/admin/permission', ['uses' => 'Admin\PermissionController@index', 'as' => 'admin.permission']);
    Route::get('/admin/permission/add', ['uses' => 'Admin\PermissionController@add', 'as' => 'admin.permission.add']);
    Route::get('/admin/permission/edit/{id}', ['uses' => 'Admin\PermissionController@edit', 'as' => 'admin.permission.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/permission/save', ['uses' => 'Admin\PermissionController@save', 'as' => 'admin.permission.save']);

    Route::get('/admin/permission-group', ['uses' => 'Admin\PermissionController@group', 'as' => 'admin.permission.group']);
    Route::get('/admin/permission-group/add', ['uses' => 'Admin\PermissionController@groupAdd', 'as' => 'admin.permission.group.add']);
    Route::get('/admin/permission-group/edit/{id}', ['uses' => 'Admin\PermissionController@groupEdit', 'as' => 'admin.permission.group.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/permission-group/save', ['uses' => 'Admin\PermissionController@groupSave', 'as' => 'admin.permission.group.save']);

    Route::get('/admin/email-template', ['uses' => 'Admin\EmailTemplateController@index', 'as' => 'admin.email.template']);
    Route::get('/admin/email-template/add', ['uses' => 'Admin\EmailTemplateController@add', 'as' => 'admin.email.template.add']);
    Route::get('/admin/email-template/edit/{id}', ['uses' => 'Admin\EmailTemplateController@edit', 'as' => 'admin.email.template.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/email-template/save', ['uses' => 'Admin\EmailTemplateController@save', 'as' => 'admin.email.template.save']);

    Route::get('/admin/email-template-group', ['uses' => 'Admin\EmailTemplateController@group', 'as' => 'admin.email.template.group']);
    Route::get('/admin/email-template-group/add', ['uses' => 'Admin\EmailTemplateController@groupAdd', 'as' => 'admin.email.template.group.add']);
    Route::get('/admin/email-template-group/edit/{id}', ['uses' => 'Admin\EmailTemplateController@groupEdit', 'as' => 'admin.email.template.group.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/email-template-group/save', ['uses' => 'Admin\EmailTemplateController@groupSave', 'as' => 'admin.email.template.group.save']);

    Route::get('/admin/term-template', ['uses' => 'Admin\TermTemplateController@index', 'as' => 'admin.term.template']);
    Route::get('/admin/term-template/add', ['uses' => 'Admin\TermTemplateController@add', 'as' => 'admin.term.template.add']);
    Route::get('/admin/term-template/edit/{id}', ['uses' => 'Admin\TermTemplateController@edit', 'as' => 'admin.term.template.edit'])->where(['id' => '[0-9]+']);
    Route::post('/admin/term-template/save', ['uses' => 'Admin\TermTemplateController@save', 'as' => 'admin.term.template.save']);

    Route::get('/auth/qb/login', ['uses' => 'Auth\QBController@login', 'as' => 'auth.qb.login']);
});
/* guest */
Route::group(['middleware' => 'guest'], function() {
    Route::get('/login/google', ['uses' => 'Auth\GoogleController@redirect', 'as' => 'login.google']);
    Route::get('/auth/google/callback', ['uses' => 'Auth\GoogleController@callback', 'as' => 'auth.google.redirect']);
});


Route::get('/auth/qb/callback', ['uses' => 'Auth\QBController@callback', 'as' => 'auth.qb.redirect']);