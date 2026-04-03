<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('setToken', [App\Http\Controllers\Auth\AjaxController::class, 'setToken'])->name('setToken');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
Route::get('lang/change', [App\Http\Controllers\LangController::class, 'change'])->name('changeLang');
Route::post('payments/razorpay/createorder', [App\Http\Controllers\RazorPayController::class, 'createOrderid']);
Route::get('register/phone', function () {
      return view('auth.phone_register');
})->name('register.phone');
    
Route::get('document-list', [App\Http\Controllers\DocumentController::class, 'DocumentList'])->name('owners.document');
Route::get('document/upload/{id}', [App\Http\Controllers\DocumentController::class, 'DocumentUpload'])->name('document.upload');
Route::post('/register-phone', [App\Http\Controllers\Auth\RegisterController::class, 'registerPhone'])->name('phone.register');
Route::get('/users/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('users.profile');
Route::post('/users/profile/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('users.profile.update');
Route::get('/drivers', [App\Http\Controllers\DriverController::class, 'index'])->name('drivers');
Route::get('/drivers/create', [App\Http\Controllers\DriverController::class, 'createDriver'])->name('drivers.create');
Route::get('/drivers/edit/{id}', [App\Http\Controllers\DriverController::class, 'edit'])->name('drivers.edit');
Route::get('/drivers/view/{id}', [App\Http\Controllers\DriverController::class, 'view'])->name('drivers.view');
Route::get('/drivers/document/{id}', [App\Http\Controllers\DriverController::class, 'driverDocuments'])->name('drivers.document');
Route::get('/drivers/document/upload/{driverId}/{id}', [App\Http\Controllers\DriverController::class, 'driverDocumentUpload'])->name('drivers.document.upload');
Route::get('/rides', [App\Http\Controllers\RidesController::class, 'index'])->name('rides');
Route::get('/rides/show/{id}', [App\Http\Controllers\RidesController::class, 'show'])->name('rides.show');
Route::get('/intercity-service-rides', [App\Http\Controllers\IntercityServiceController::class, 'ridesList'])->name('intercity-service-rides');
Route::get('/intercity-service-rides/view/{id}', [App\Http\Controllers\IntercityServiceController::class, 'rideView'])->name('intercity-service-rides.view');
Route::get('/reports/{type}', [App\Http\Controllers\ReportController::class, 'reportGenerate'])->name('reports');
Route::get('/walletTransactions', [App\Http\Controllers\TransactionController::class, 'ownerWalletTranscation'])->name('walletTransaction.owner');
Route::post('send-notification', [App\Http\Controllers\NotificationController::class, 'sendNotification'])->name('send-notification');
Route::get('/driver/subscription-plan/history', [App\Http\Controllers\SubscriptionPlanController::class, 'SubscriptionHistory'])->name('driver.subscriptionHistory');

//API Url for app
Route::post('payments/getpaytmchecksum', [App\Http\Controllers\PaymentController::class, 'getPaytmChecksum']);
Route::post('payments/validatechecksum', [App\Http\Controllers\PaymentController::class, 'validateChecksum']);
Route::post('payments/initiatepaytmpayment', [App\Http\Controllers\PaymentController::class, 'initiatePaytmPayment']);
Route::get('payments/paytmpaymentcallback', [App\Http\Controllers\PaymentController::class, 'paytmPaymentcallback']);
Route::post('payments/paypalclientid', [App\Http\Controllers\PaymentController::class, 'getPaypalClienttoken']);
Route::post('payments/paypaltransaction', [App\Http\Controllers\PaymentController::class, 'createBraintreePayment']);
Route::post('payments/stripepaymentintent', [App\Http\Controllers\PaymentController::class, 'createStripePaymentIntent']);
Route::get('payment/success', [App\Http\Controllers\PaymentController::class, 'paymentsuccess'])->name('payment.success');
Route::get('payment/failed', [App\Http\Controllers\PaymentController::class, 'paymentfailed'])->name('payment.failed');
Route::get('payment/pending', [App\Http\Controllers\PaymentController::class, 'paymentpending'])->name('payment.pending');
Route::post('store-firebase-service', [App\Providers\FirebaseAuthService::class, 'storeFirebaseService'])->name('store-firebase-service');
Route::post('get-firebase-token', [App\Providers\FirebaseAuthService::class, 'getFirebaseToken']);
Route::get('subscription-plan', [App\Http\Controllers\SubscriptionController::class, 'show'])->name('subscription-plan.show');
Route::get('subscription-plan/checkout/{id}', [App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('subscription-plans.checkout');
Route::post('payment-proccessing', [App\Http\Controllers\SubscriptionController::class, 'orderProccessing'])->name('payment-proccessing');
Route::get('pay-subscription', [App\Http\Controllers\SubscriptionController::class, 'proccesstopay'])->name('pay-subscription');
Route::post('order-complete', [App\Http\Controllers\SubscriptionController::class, 'orderComplete'])->name('order-complete');
Route::post('process-stripe', [App\Http\Controllers\SubscriptionController::class, 'processStripePayment'])->name('process-stripe');
Route::post('process-paypal', [App\Http\Controllers\SubscriptionController::class, 'processPaypalPayment'])->name('process-paypal');
Route::post('razorpaypayment', [App\Http\Controllers\SubscriptionController::class, 'razorpaypayment'])->name('razorpaypayment');
Route::post('process-mercadopago', [App\Http\Controllers\SubscriptionController::class, 'processMercadoPagoPayment'])->name('process-mercadopago');
Route::get('success', [App\Http\Controllers\SubscriptionController::class, 'success'])->name('success');
Route::get('failed', [App\Http\Controllers\SubscriptionController::class, 'failed'])->name('failed');
Route::get('notify', [App\Http\Controllers\SubscriptionController::class, 'notify'])->name('notify');
Route::post('setSubcriptionFlag', [App\Http\Controllers\Auth\AjaxController::class, 'setSubcriptionFlag'])->name('setSubcriptionFlag');
Route::get('/payouts', [App\Http\Controllers\PayoutsController::class, 'index'])->name('payout');
Route::get('/payout/create', [App\Http\Controllers\PayoutsController::class, 'create'])->name('payout.create');
Route::get('/payout/edit/{id}', [App\Http\Controllers\PaymentController::class, 'edit'])->name('payout.edit');