<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EarningsController;
use App\Http\Controllers\ArtistLabelController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\StatsController;


// Public Home Page
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
    Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy.policy');
    Route::get('/cookies', [HomeController::class, 'cookies'])->name('cookies.page');
    Route::get('/refund', [HomeController::class, 'refund'])->name('refund.page');
    Route::get('/payment/callback/{gateway}', [PaymentController::class, 'handleCallback'])->name('payment.callback');
    Route::post('/webhook/paypal', [PaymentController::class, 'paypalWebhook'])->name('paypal.webhook');
    Route::post('/webhook/nowpayments', [PaymentController::class, 'handleNowPaymentCallback'])->name('nowpayments.webhook');


Route::middleware(['auth', 'verified', 'sub', 'active'])->group(function () {
   
    Route::get('/dashboard', [UserController::class, 'userDashboard'])->name('user.dashboard');
    Route::get('/releases', [UserController::class, 'release'])->name('user.releases');
    Route::get('/releases/{project}', [UserController::class, 'showRelease']);

    // User Settings Routes
    Route::get('/stats', [StatsController::class, 'index'])->name('user.stats');
    Route::get('/stats/release/{project}', [StatsController::class, 'release'])->name('user.stats.release');
    Route::get('/stats/{music}', [StatsController::class, 'show'])->name('user.stats.show');
    Route::get('/settings', [UserController::class, 'settings'])->name('user.settings');
    Route::post('/settings', [UserController::class, 'updateSettings'])->name('user.settings.update');

 // Authenticated User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/royalties', [EarningsController::class, 'index'])
    ->name('royalties.index');
    Route::get('/earnings/withdraw', [WithdrawalController::class, 'create'])->name('earnings.withdraw'); 
    
    Route::post('/earnings/withdraw', [WithdrawalController::class, 'store'])->name('earnings.withdraw.store');
    
    

    //Music Upload
     Route::get('/music/upload', [MusicController::class, 'create'])->name('music.upload');
    Route::post('/music/store', [MusicController::class, 'store'])->name('music.store');
    
    Route::post('/music/upload-cover', [MusicController::class, 'uploadCover'])->name('music.uploadCover');
    Route::post('/music/upload-audio', [MusicController::class, 'uploadAudio'])->name('music.uploadAudio');
    Route::post('/music/upload-chunk', [MusicController::class, 'uploadChunk'])->name('music.uploadChunk');
    
});
// Normal User Dashboard
Route::middleware(['auth', 'verified'])->prefix('payment')->name('payment.')->group(function () {
    
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::post('/process', [PaymentController::class, 'process'])->name('process');
    Route::get('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::get('/manual', [PaymentController::class, 'manualInstructions'])->name('manual');
    Route::post('/manual/submit', [PaymentController::class, 'submitManualPayment'])->name('manual.submit');
    Route::get('/moneyunify/wait/{transaction_id}', [PaymentController::class, 'wait'])->name('moneyunify.wait');
    Route::get('/moneyunify/check/{transaction_id}', [PaymentController::class, 'check'])->name('moneyunify.check');
    });



// Authenticated Artist Management
Route::middleware(['auth', 'role:label', 'verified', 'sub', 'active'])->group(function () {
    Route::get('/artists', [ArtistLabelController::class, 'index'])->name('artists.index');
    Route::get('/artists/create', [ArtistLabelController::class, 'create'])->name('artists.create');
    Route::post('/artists', [ArtistLabelController::class, 'store'])->name('artists.store');
    Route::get('/artists/{artist}/edit', [ArtistLabelController::class, 'edit'])->name('artists.edit');
    Route::put('/artists/{artist}', [ArtistLabelController::class, 'update'])->name('artists.update');
});

Route::get('/sitemap.xml', function () {
    $path = base_path('sitemap.xml');
    if (!file_exists($path)) {
        \App\Services\SitemapGenerator::generate();
    }
    return response()->file($path, ['Content-Type' => 'application/xml']);
});
// Auth scaffolding (Laravel Breeze / Jetstream / Fortify etc.)
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/installer.php';
