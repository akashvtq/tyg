<?php

use App\Http\Controllers\Common\LocaleController;
use App\Http\Controllers\Frontend\BlogController as FrontendBlogController;
use App\Http\Controllers\Frontend\ContactController as FrontendContactController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\DepositController;
use App\Http\Controllers\Frontend\HomeController as FrontendHomeController;
use App\Http\Controllers\Frontend\CompanyController;
use App\Http\Controllers\Frontend\IPNController;
use App\Http\Controllers\Frontend\NotificationController;
use App\Http\Controllers\Frontend\PageController as FrontendPageController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\SeoPageController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\StatusController;
use App\Http\Controllers\Frontend\SubscriptionController;
use App\Http\Controllers\Frontend\TicketController;
use App\Http\Controllers\Frontend\TransactionController;
use App\Models\Page;
use App\Models\SeoPage;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Handles authenticated user-specific actions like dashboard access
| and deposit management. Middleware ensures users are authenticated
| and email-verified before accessing these routes.
*/

// User Profile and Dashboard
Route::group(['prefix' => 'user', 'as' => 'user.', 'middleware' => ['auth', 'check.status:web', 'verified', '2fa']], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management Routes
    Route::group(['prefix' => 'profile', 'as' => 'profile.', 'controller' => ProfileController::class], function () {
        Route::get('/', 'index')->name('index');
        Route::post('update', 'update')->name('update');
        Route::get('change-password', 'changePassword')->name('change-password');
        Route::post('update-password', 'updatePassword')->name('update-password');
        Route::get('verify-email', 'verifyEmail')->name('verify-email');
        Route::get('security', 'security')->name('security');
        Route::get('/2fa-generate', 'twoFaAuthGenerate')->name('2fa-generate');
        Route::post('/2fa', 'twoFaAuth')->name('2fa-auth');
        Route::post('/2fa-verify', 'twoFaAuthVerify')->name('2fa-verify');
    });

    // Service Management Routes
    Route::group(['prefix' => 'service', 'as' => 'service.', 'controller' => ServiceController::class], function () {
        Route::get('book-now/{slug}', 'bookService')->name('book-now');
        Route::post('store', 'store')->name('store');
        Route::get('my', 'myService')->name('my');
        Route::get('task-board/{service_id}', 'taskBoard')->name('task-board');
        Route::get('task-show/{task_id}', 'taskShow')->name('task-show');
        Route::get('task-file/{task_id}', 'taskFile')->name('task-file');
    });

    //Service with projects & Order
    Route::group(['prefix' => 'order', 'as' => 'order.', 'controller' => OrderController::class], function () {
         Route::get('services', 'orderServices')->name('services');
    });

    // Subscription Management Routes
    Route::group(['prefix' => 'subscription', 'as' => 'subscription.', 'controller' => SubscriptionController::class], function () {
        Route::get('request-now', 'requestNow')->name('request');
        Route::post('store', 'store')->name('store');
        Route::get('my', 'mySubscription')->name('my');
        Route::get('upgrade', 'upgrade')->name('upgrade');
        Route::get('renew', 'renew')->name('renew');
    });

    // Deposit Management Routes
    Route::group(['prefix' => 'deposit', 'as' => 'deposit.', 'controller' => DepositController::class], function () {
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('history', 'history')->name('history');
    });

    //Transaction Management Routes
    Route::get('transaction', [TransactionController::class, 'transaction'])->name('transaction');

    //Support Management Routes
    Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.', 'controller' => TicketController::class], function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('show/{ticket}', 'show')->name('show');
        Route::post('reply/{ticket}', 'reply')->name('reply');
        Route::get('close/{ticket}', 'close')->name('close');
    });

    // Notification Management Routes
    Route::controller(NotificationController::class)->prefix('notifications')->as('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('recent', 'recent')->name('recent');
        Route::get('{id}/read', 'markAsRead')->name('markAsRead');
        Route::get('read-all', 'markAllAsRead')->name('read-all');
    });

});

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
|
| These routes handle Instant Payment Notification (IPN) and payment statuses
| like success, cancellation, and pending payments. IPN routes support both
| GET and POST requests to accommodate various gateways.
*/

// Instant Payment Notification (IPN)
Route::match(['get', 'post'], '/ipn/{gateway}', [IPNController::class, 'handleIPN'])->name('ipn.handle');

// Payment Status Routes
Route::group(['controller' => StatusController::class, 'prefix' => 'status', 'as' => 'status.'], function () {
    Route::match(['get', 'post'], 'success', 'success')->name('success');
    Route::match(['get', 'post'], 'cancel', 'cancel')->name('cancel');
    Route::match(['get', 'post'], 'pending', 'pending')->name('pending');
});

/*
|--------------------------------------------------------------------------
| Common Routes
|--------------------------------------------------------------------------
|
| These routes manage functionalities available to all users, such as
| locale switching.
*/

// Locale Management
Route::get('locale-set/{locale}', [LocaleController::class, 'setLocale'])->name('locale-set');

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
|
| These routes handle the frontend area of the application.
| They are accessible by both authenticated and unauthenticated users.
| Routes include homepage, dynamic pages, and content details.
*/

// Homepage and Dynamic Pages
Route::get('/', [FrontendHomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'index'])->name('site.sitemap');
Route::get('/seo-services', [\App\Http\Controllers\Frontend\TygController::class, 'seoServices'])->name('tyg.seo-services');
Route::get('/branding-services', [\App\Http\Controllers\Frontend\TygController::class, 'brandingServices'])->name('tyg.branding-services');
Route::get('/development-services', [\App\Http\Controllers\Frontend\TygController::class, 'developmentServices'])->name('tyg.development-services');
Route::get('/{slug}', [FrontendPageController::class, 'page'])->name('page')
    ->where('slug', Page::getValidSlugs());
Route::get('details/{id}', [FrontendPageController::class, 'details'])->name('content.details');

// Company Details
Route::get('/software/{slug}', [CompanyController::class, 'index'])->name('company.details');
Route::get('/programmatic-seo-services', [CompanyController::class, 'seoServices'])->name('company.seoServices');

// SEO Pages: /seo, /seo/{subcategory}, /seo/{subcategory}/{topic}
// New categories need their slug added to SeoPage::ROUTED_CATEGORIES.
Route::get('/guide', [SeoPageController::class, 'index'])->name('seo-page.guide');
Route::get('/guides', [SeoPageController::class, 'index'])->name('seo-page.index');
Route::get('/sitemap-seo.xml', [SeoPageController::class, 'sitemap'])->name('seo-page.sitemap');
Route::get('/{category}/{subcategory?}/{topic?}', [SeoPageController::class, 'show'])
    ->where('category', implode('|', SeoPage::ROUTED_CATEGORIES))
    ->name('seo-page.show');

// Blog Management Routes
Route::group(['prefix' => 'blog', 'as' => 'blog.', 'controller' => FrontendBlogController::class], function () {
    Route::get('filter', 'filter')->name('filter');
    Route::get('{slug}', 'details')->name('details');
});

Route::group(['prefix' => 'service', 'as' => 'service.', 'controller' => ServiceController::class], function () {
    Route::get('{slug}', 'details')->name('details');
});

// Contact Us Route
Route::post('contact', [FrontendContactController::class, 'contactUs'])->name('contact.us');
