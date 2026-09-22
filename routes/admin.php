<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminMusicController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\RoyaltiesController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\SettingsController;

use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\UserSubscriptionController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\HomepageContentController;


// Admin Login Routes
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');

// Authenticated Admin Routes
Route::prefix('admin')->middleware(['auth:admin'])->name('admin.')->group(function () {

  // Dashboard
  Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
  
  // Profile
    Route::get('/profile', [AdminController::class, 'edit'])->name('profile.edit');
    
    Route::post('/profile', [AdminController::class, 'update'])->name('profile.update');

  // Logout
  Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

  // Users
  Route::prefix('users')->name('users.')->group(function () {
    Route::get('/all', [AdminController::class, 'userIndex'])->name('all');
    Route::post('/add', [AdminController::class, 'userAdd'])->name('add');

    Route::put('/{id}', [AdminController::class, 'userUpdate'])->name('update');
    Route::delete('/{id}', [AdminController::class, 'userDestroy'])->name('destroy');

     });

  Route::prefix('users')->name('labels.')->group(function () {
    Route::get('/', [AdminController::class, 'labels'])->name('all');
    Route::get('/artists', [AdminController::class, 'artists'])->name('artists');
    Route::get('/{id}', [AdminController::class, 'show'])->name('show');
  });

  Route::prefix('music')->name('releases.')->group(function () {
    Route::get('/all', [AdminMusicController::class, 'index'])->name('all');
    
    Route::post('/approve/{id}', [AdminMusicController::class, 'approveProject'])->name('approve');
  Route::post('/reject/{id}', [AdminMusicController::class, 'rejectProject'])->name('reject');
  Route::delete('/{release}', [AdminMusicController::class, 'destroy'])
    ->name('delete');


    // Status filters
    Route::get('/pending', [AdminMusicController::class, 'status'])->name('pending')->defaults('status', 'pending');
    Route::get('/approved', [AdminMusicController::class, 'status'])->name('approved')->defaults('status', 'approved');
    Route::get('/rejected', [AdminMusicController::class, 'status'])->name('rejected')->defaults('status', 'rejected');

    // Downloads
    Route::get('/{release}/download-metadata', [AdminMusicController::class, 'downloadMetadata'])->name('metadata.download');
    Route::get('/{release}/download-audio', [AdminMusicController::class, 'downloadAudio'])->name('audio.download');
    Route::get('/{release}/download-cover', [AdminMusicController::class, 'downloadCover'])->name('cover.download');
    Route::get('/{release}/stream-audio', [AdminMusicController::class, 'streamAudio'])->name('audio.stream');

    // Metadata edit
    Route::get('/metadata/{project}', [AdminMusicController::class, 'metadataEdit'])->name('metadata');
    Route::post('/metadata/{project}', [AdminMusicController::class, 'updateMetadata'])->name('metadata.update');

    // Catch-all: show a single release
    Route::get('/{release}', [AdminMusicController::class, 'show'])->name('show');
  });


  // Pricing and Subscriptions
  Route::prefix('pricing')->name('pricing.')->group(function () {
    Route::get('/', [SubscriptionPlanController::class, 'plans'])->name('plans');
    Route::get('/create', [SubscriptionPlanController::class, 'create'])->name('create');
    Route::post('/store', [SubscriptionPlanController::class, 'store'])->name('store');

    Route::get('/{plan}/edit', [SubscriptionPlanController::class, 'editPlan'])->name('edit');
    Route::put('/{plan}', [SubscriptionPlanController::class, 'updatePlan'])->name('update');

    Route::delete('/{plan}', [SubscriptionPlanController::class, 'deletePlan'])->name('destroy');
  });
  

Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
    Route::get('/active', [UserSubscriptionController::class, 'active'])
        ->name('active');
    Route::get('/history', [UserSubscriptionController::class, 'history'])
        ->name('history');
    Route::get('/user/{user}', [UserSubscriptionController::class, 'userSubscription'])
        ->name('user');
});

// Manual Payments (admin review / approval)
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/manual', [AdminController::class, 'manualTransactions'])->name('manual');
    Route::post('/manual/{transaction}/approve', [AdminController::class, 'approveManual'])->name('manual.approve');
    Route::post('/manual/{transaction}/reject', [AdminController::class, 'rejectManual'])->name('manual.reject');
});

Route::prefix('analytics')->name('analytics.')->group(function () {
 
    Route::get('/dashboard',
        [AnalyticsController::class, 'dashboard']
    )->name('dashboard');
 
    Route::get('/manual-entry',
        [AnalyticsController::class, 'manualEntryForm']
    )->name('manual.form');
 
    Route::post('/manual-entry',
        [AnalyticsController::class, 'storeManual']
    )->name('manual.store');
 
});

  // Royalties
Route::prefix('royalties')->name('royalties.')->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────
    Route::get('/reports', [RoyaltiesController::class, 'reports'])
        ->name('reports');

    // ── Stream Reports ────────────────────────────────────────────
    Route::get('/reports/streams', [RoyaltiesController::class, 'streamsReport'])
        ->name('reports.streams');

    Route::get('/reports/stream-detail', [RoyaltiesController::class, 'streamDetail'])
        ->name('reports.streams.detail');

    // ── Earnings Report ───────────────────────────────────────────
    Route::get('/reports/earnings', [RoyaltiesController::class, 'earningsReport'])
        ->name('reports.earnings');

    Route::post('/earnings/{balance}/approve', [RoyaltiesController::class, 'approveEarnings'])
        ->name('earnings.approve');

    Route::post('/earnings/{balance}/reject', [RoyaltiesController::class, 'rejectEarnings'])
        ->name('earnings.reject');

    Route::post('/earnings/mass-approve', [RoyaltiesController::class, 'massApprove'])
        ->name('earnings.mass.approve');

    Route::post('/earnings/mass-reject', [RoyaltiesController::class, 'massReject'])
        ->name('earnings.mass.reject');

    // ── CSV Upload (from distributor) ─────────────────────────────
    Route::post('/reports/upload', [RoyaltiesController::class, 'uploadReport'])
        ->name('reports.upload');

    // ── Export & Send Report (ReportController) ───────────────────
    Route::get('/reports/export/artists-monthly', [ReportController::class, 'exportArtistMonthly'])
        ->name('reports.export.artists.monthly');

    Route::post('/reports/send-report', [ReportController::class, 'sendReportToArtist'])
        ->name('reports.send');

});
  
   // Withdrawals
  Route::prefix('withdrawals')->name('withdrawals.')->group(function () {

    Route::get('/', [WithdrawalController::class, 'index'])->name('pending');
    
    Route::get('/history', [WithdrawalController::class, 'history'])->name('history');
    Route::post('/{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('approve');
    Route::post('/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('reject');

});


  // Homepage Content
  Route::prefix('homepage')->name('homepage.')->group(function () {
    Route::get('/', [HomepageContentController::class, 'index'])->name('index');
    Route::post('/', [HomepageContentController::class, 'update'])->name('update');
  });

  // Settings
  Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/general', [SettingsController::class, 'general'])->name('general');
    Route::post('/general', [SettingsController::class, 'updateGeneral'])->name('update');
     Route::get('/currencies', [SettingsController::class, 'currencyIndex'])->name('currencies');
     
     Route::post('/currencies', [SettingsController::class, 'storeCurrency'])->name('currencies.store');

    Route::put('/currencies/{currency}', [SettingsController::class, 'updateCurrency'])->name('currencies.update');

    Route::delete('/currencies/{currency}', [SettingsController::class, 'destroyCurrency'])->name('currencies.destroy');

    Route::get('/payment', [PaymentGatewayController::class, 'edit'])->name('payment');
    Route::put('payment-gateways/update', [PaymentGatewayController::class, 'update'])->name('payment-gateways.update');
    Route::get('/terms', [SettingsController::class, 'editTerms'])
        ->name('terms');
    Route::post('/terms', [SettingsController::class, 'updateTerms'])
        ->name('terms.update');
    Route::get('/privacy-policy',  [SettingsController::class, 'editPrivacyPolicy'])->name('privacy');
    Route::post('/privacy-policy', [SettingsController::class, 'updatePrivacyPolicy'])->name('privacy.update');
    // Cookie Policy
Route::get('/cookies', [SettingsController::class, 'editCookies'])->name('cookies');
Route::post('/cookies', [SettingsController::class, 'updateCookies'])->name('cookies.update');

// Refund Policy
Route::get('/refund-policy', [SettingsController::class, 'editRefund'])->name('refund');
Route::post('/refund-policy', [SettingsController::class, 'updateRefund'])->name('refund.update');
    
  });
});