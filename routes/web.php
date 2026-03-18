<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// V1.0.2 - DEFINITIVE ROUTE FIX (Closing Admin group properly)

Route::get('/setup', [\App\Http\Controllers\SetupController::class, 'index'])->name('setup.index');
Route::post('/setup', [\App\Http\Controllers\SetupController::class, 'setup'])->name('setup.save');
Route::post('/setup/migrate', [\App\Http\Controllers\SetupController::class, 'migrate'])->name('setup.migrate');

Route::get('/', function () {
    $preview = request()->has('preview') && auth()->check() && auth()->user()->is_admin;
    $sections = \App\Models\HomeSection::orderBy('order')
        ->when(!$preview, function ($query) {
            return $query->where('status', 'published');
        }
        )
            ->get();
        return view('welcome', compact('sections'));
    })->name('home');

Route::get('/about', [\App\Http\Controllers\Frontend\PageController::class , 'show'])->defaults('slug', 'about')->name('about');
Route::get('/contact', [\App\Http\Controllers\Frontend\PageController::class , 'show'])->defaults('slug', 'contact')->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class , 'store'])->name('contact.store');
Route::get('/privacy-policy', [\App\Http\Controllers\Frontend\PageController::class , 'show'])->defaults('slug', 'privacy-policy')->name('privacy');
Route::get('/terms', [\App\Http\Controllers\Frontend\PageController::class , 'show'])->defaults('slug', 'terms')->name('terms');
Route::get('/refund-policy', [\App\Http\Controllers\Frontend\PageController::class , 'show'])->defaults('slug', 'refund-policy')->name('refund');

Route::get('/dashboard', function () {
    if (auth()->user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('client.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/blog', [\App\Http\Controllers\Frontend\BlogController::class , 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\Frontend\BlogController::class , 'show'])->name('blog.show');

Route::get('/page/{slug}', [\App\Http\Controllers\Frontend\PageController::class , 'show'])->name('page.show');

Route::get('/services', [\App\Http\Controllers\Frontend\ServiceController::class , 'index'])->name('services.index');
Route::get('/services/category/{category:slug}', [\App\Http\Controllers\Frontend\ServiceController::class , 'category'])->name('services.category');
Route::post('/services/coupon/check', [\App\Http\Controllers\Frontend\ServiceController::class , 'checkCoupon'])->name('services.coupon.check');
Route::get('/services/{service:slug}', [\App\Http\Controllers\Frontend\ServiceController::class , 'show'])->name('services.show');
Route::post('/services/{package}/checkout', [\App\Http\Controllers\Frontend\ServiceController::class , 'checkout'])->name('services.checkout')->middleware(['auth']);

Route::get('/guest-posts', [\App\Http\Controllers\Frontend\GuestPostController::class , 'index'])->name('guest_posts.index');
Route::post('/guest-posts/{guestPost}/checkout', [\App\Http\Controllers\Frontend\GuestPostController::class , 'checkout'])->name('guest_posts.checkout')->middleware(['auth']);

Route::get('/website-traffic', [\App\Http\Controllers\Frontend\WebsiteTrafficController::class , 'index'])->name('traffic.index');
Route::get('/website-traffic/category/{category:slug}', [\App\Http\Controllers\Frontend\ServiceController::class , 'category'])->name('traffic.category');
Route::get('/website-traffic/{service:slug}', [\App\Http\Controllers\Frontend\ServiceController::class , 'show'])->name('traffic.show');
Route::post('/website-traffic/{package}/checkout', [\App\Http\Controllers\Frontend\ServiceController::class , 'checkout'])->name('traffic.checkout')->middleware(['auth']);

// Referral Link Tracking
Route::get('/ref/{code}', [\App\Http\Controllers\ReferralController::class , 'redirect'])->name('referral.redirect');

// Stripe Webhook Endpoint (No CSRF)
Route::post('/stripe/webhook', [\App\Http\Controllers\Payments\StripeWebhookController::class , 'handleWebhook'])->name('stripe.webhook');

// Plisio Callback Endpoint (No CSRF)
Route::post('/plisio/callback', [\App\Http\Controllers\Payments\PlisioCallbackController::class , 'handleCallback'])->name('plisio.callback');

// Technical SEO Routes
Route::get('/robots.txt', [\App\Http\Controllers\SeoController::class , 'robots']);
Route::get('/sitemap.xml', [\App\Http\Controllers\SeoController::class , 'sitemap']);

// Dynamic Campaign Routes (Frontend)
Route::get('/campaigns/{type}', [\App\Http\Controllers\Frontend\CampaignController::class , 'index'])->name('campaigns.index');
Route::get('/campaigns/{type}/category/{category:slug}', [\App\Http\Controllers\Frontend\ServiceController::class , 'category'])->name('campaigns.category');
Route::get('/campaigns/{type}/{service:slug}', [\App\Http\Controllers\Frontend\CampaignController::class , 'show'])->name('campaigns.show');
Route::post('/campaigns/{type}/{package}/checkout', [\App\Http\Controllers\Frontend\CampaignController::class , 'checkout'])->name('campaigns.checkout')->middleware(['auth']);

// Dedicated Link Building Routes (clean slug: /link-building/)
Route::get('/link-building', [\App\Http\Controllers\Frontend\CampaignController::class , 'index'])->defaults('type', 'link-building')->name('link_building.index');
Route::get('/link-building/category/{category:slug}', [\App\Http\Controllers\Frontend\ServiceController::class , 'category'])->defaults('typePrefix', 'link-building')->name('link_building.category');
Route::get('/link-building/{service:slug}', function (\App\Models\Service $service) {
    return app(\App\Http\Controllers\Frontend\CampaignController::class)->show('link-building', $service);
})->name('link_building.show');
Route::post('/link-building/{package}/checkout', function (\Illuminate\Http\Request $request, \App\Models\Package $package) {
    return app(\App\Http\Controllers\Frontend\CampaignController::class)->checkout($request, 'link-building', $package);
})->name('link_building.checkout')->middleware(['auth']);

Route::middleware(['auth'])->group(function () {
    // Universal Order Messages & Inbox
    Route::post('/orders/{order}/messages', [\App\Http\Controllers\OrderMessageController::class , 'store'])->name('orders.messages.store');
    Route::get('/inbox', [\App\Http\Controllers\CommunicationController::class , 'index'])->name('inbox');
    Route::get('/inbox/messages', [\App\Http\Controllers\CommunicationController::class , 'messages'])->name('inbox.messages');
    Route::post('/support/messages', [\App\Http\Controllers\SupportMessageController::class , 'store'])->name('support.messages.store');
    Route::redirect('/messages', '/inbox');

    // Admin Routes
    Route::middleware(\App\Http\Middleware\IsAdmin::class)->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class , 'index'])->name('dashboard');

            // Site Settings
            Route::get('/site-settings', [\App\Http\Controllers\Admin\SettingController::class , 'index'])->name('site-settings.index');
            Route::post('/site-settings', [\App\Http\Controllers\Admin\SettingController::class , 'store'])->name('site-settings.store');

            Route::resource('/services', \App\Http\Controllers\Admin\ServiceController::class);
            Route::resource('/coupons', \App\Http\Controllers\Admin\CouponController::class);
            Route::resource('/traffic', \App\Http\Controllers\Admin\WebsiteTrafficController::class)->parameters(['traffic' => 'traffic']);
            Route::resource('/link-building', \App\Http\Controllers\Admin\LinkBuildingController::class)->parameters(['link-building' => 'linkBuilding']);

            // Dynamic Campaign Routes
            Route::group(['prefix' => 'campaigns/{type}', 'as' => 'campaigns.'], function () {
                    Route::get('/', [\App\Http\Controllers\Admin\CampaignController::class , 'index'])->name('index');
                    Route::get('/create', [\App\Http\Controllers\Admin\CampaignController::class , 'create'])->name('create');
                    Route::post('/', [\App\Http\Controllers\Admin\CampaignController::class , 'store'])->name('store');
                    Route::get('/{campaign}/edit', [\App\Http\Controllers\Admin\CampaignController::class , 'edit'])->name('edit');
                    Route::put('/{campaign}', [\App\Http\Controllers\Admin\CampaignController::class , 'update'])->name('update');
                    Route::delete('/{campaign}', [\App\Http\Controllers\Admin\CampaignController::class , 'destroy'])->name('destroy');
                }
                );

                Route::resource('/categories', \App\Http\Controllers\Admin\CategoryController::class);
                Route::resource('/posts', \App\Http\Controllers\Admin\PostController::class);
                Route::resource('/pages', \App\Http\Controllers\Admin\PageController::class);
                Route::get('/orders/running', [\App\Http\Controllers\Admin\OrderController::class, 'running'])->name('orders.running');
                Route::resource('/orders', \App\Http\Controllers\Admin\OrderController::class);
                Route::resource('/guest-posts', \App\Http\Controllers\Admin\GuestPostSiteController::class)->parameters(['guest-posts' => 'guestPost']);
                Route::resource('/invoices', \App\Http\Controllers\Admin\InvoiceController::class);
                Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\Admin\InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
                Route::post('/invoices/{invoice}/send-email', [\App\Http\Controllers\Admin\InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
                Route::post('/invoices/{invoice}/status', [\App\Http\Controllers\Admin\InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
                Route::resource('/invoice-services', \App\Http\Controllers\Admin\InvoiceServiceController::class)->parameters(['invoice-services' => 'invoiceService']);

                Route::resource('/leads', \App\Http\Controllers\Admin\LeadController::class);
                Route::resource('/site-faqs', \App\Http\Controllers\Admin\SiteFaqController::class);
                Route::resource('/users', \App\Http\Controllers\Admin\UserController::class);
                Route::resource('/staff', \App\Http\Controllers\Admin\StaffController::class)->except(['show']);
                Route::post('/media/sync', [\App\Http\Controllers\Admin\MediaController::class, 'sync'])->name('media.sync');
                Route::delete('/media/bulk-destroy', [\App\Http\Controllers\Admin\MediaController::class, 'bulkDestroy'])->name('media.bulk-destroy');
                Route::resource('/media', \App\Http\Controllers\Admin\MediaController::class)->parameters(['media' => 'media'])->except(['show', 'create', 'edit']);

                // Home Page Management
                Route::prefix('home-sections')->name('home-sections.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\Admin\HomeSectionController::class , 'index'])->name('index');
                    Route::get('/{homeSection}/edit', [\App\Http\Controllers\Admin\HomeSectionController::class , 'edit'])->name('edit');
                    Route::put('/{homeSection}', [\App\Http\Controllers\Admin\HomeSectionController::class , 'update'])->name('update');
                    Route::post('/update-order', [\App\Http\Controllers\Admin\HomeSectionController::class , 'updateOrder'])->name('update-order');
                    Route::post('/{homeSection}/toggle-status', [\App\Http\Controllers\Admin\HomeSectionController::class , 'toggleStatus'])->name('toggle-status');
                }
                );

                // Payments Hub
                Route::group(['prefix' => 'payments', 'as' => 'payments.'], function () {
                    Route::get('/', [\App\Http\Controllers\Admin\PaymentController::class , 'index'])->name('index');
                    Route::get('/transactions', [\App\Http\Controllers\Admin\PaymentController::class , 'transactions'])->name('transactions');
                    Route::get('/topups', [\App\Http\Controllers\Admin\PaymentController::class , 'topups'])->name('topups');
                    Route::post('/topups/{topupRequest}/approve', [\App\Http\Controllers\Admin\PaymentController::class , 'approveTopup'])->name('topups.approve');
                    Route::post('/topups/{topupRequest}/reject', [\App\Http\Controllers\Admin\PaymentController::class , 'rejectTopup'])->name('topups.reject');
                    Route::post('/wallet/adjust', [\App\Http\Controllers\Admin\PaymentController::class , 'adjustWallet'])->name('wallet.adjust');
                }
                );

                // Gateway Settings
                Route::get('/payments/gateway-settings', [\App\Http\Controllers\Admin\GatewaySettingController::class , 'index'])->name('gateway-settings.index');
                Route::post('/payments/gateway-settings', [\App\Http\Controllers\Admin\GatewaySettingController::class , 'update'])->name('gateway-settings.update');

                Route::resource('/support', \App\Http\Controllers\Admin\SupportTicketController::class)->parameters(['support' => 'ticket'])->only(['index', 'show', 'update']);
                Route::resource('/announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->except(['show', 'edit', 'update']);
                Route::get('/finance', [\App\Http\Controllers\Admin\FinanceController::class , 'index'])->name('finance.index');
                Route::get('/communications', [\App\Http\Controllers\CommunicationController::class , 'index'])->name('communications.index');
                // System Updates
                Route::get('/updates', [\App\Http\Controllers\Admin\UpdateController::class, 'index'])->name('updates.index');
                Route::post('/updates/check', [\App\Http\Controllers\Admin\UpdateController::class, 'check'])->name('updates.check');
                Route::post('/updates/apply', [\App\Http\Controllers\Admin\UpdateController::class, 'update'])->name('updates.apply');
                Route::get('/updates/{log}', [\App\Http\Controllers\Admin\UpdateController::class, 'showLog'])->name('updates.show');

                Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class , 'index'])->name('analytics.index');

                // Affiliate Management
                Route::get('/affiliates', [\App\Http\Controllers\Admin\AffiliateController::class , 'index'])->name('affiliates.index');
                Route::post('/affiliates/settings', [\App\Http\Controllers\Admin\AffiliateController::class , 'settings'])->name('affiliates.settings');
                Route::get('/affiliates/{affiliate}', [\App\Http\Controllers\Admin\AffiliateController::class , 'show'])->name('affiliates.show');

                // Notification Hub
                Route::prefix('notifications')->name('notifications.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class , 'index'])->name('index');
                    Route::get('/logs', [\App\Http\Controllers\Admin\NotificationController::class , 'logs'])->name('logs');
                    Route::get('/settings', [\App\Http\Controllers\Admin\NotificationController::class , 'settings'])->name('settings');
                    Route::post('/settings', [\App\Http\Controllers\Admin\NotificationController::class , 'updateSettings'])->name('settings.update');
                    Route::post('/test-email', [\App\Http\Controllers\Admin\NotificationController::class , 'testEmail'])->name('test-email');

                    Route::resource('/templates', \App\Http\Controllers\Admin\EmailTemplateController::class)->names([
                        'index' => 'templates.index',
                        'create' => 'templates.create',
                        'store' => 'templates.store',
                        'edit' => 'templates.edit',
                        'update' => 'templates.update',
                        'destroy' => 'templates.destroy',
                    ]);
                }
                );

                // SEO Manager
                Route::prefix('seo-manager')->name('seo.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\Admin\SeoManagerController::class , 'index'])->name('index');
                    Route::get('/settings', [\App\Http\Controllers\Admin\SeoSettingsController::class , 'index'])->name('settings');
                    Route::post('/settings', [\App\Http\Controllers\Admin\SeoSettingsController::class , 'update'])->name('settings.update');
                    Route::get('/analytics', [\App\Http\Controllers\Admin\SeoSettingsController::class , 'analytics'])->name('analytics');
                    Route::post('/analytics', [\App\Http\Controllers\Admin\SeoSettingsController::class , 'updateAnalytics'])->name('analytics.update');
                    Route::get('/robots', [\App\Http\Controllers\Admin\SeoSettingsController::class , 'robots'])->name('robots');
                    Route::post('/robots', [\App\Http\Controllers\Admin\SeoSettingsController::class , 'updateRobots'])->name('robots.update');
                    Route::get('/sitemap', [\App\Http\Controllers\Admin\SeoSettingsController::class , 'sitemap'])->name('sitemap');
                    Route::post('/sitemap/regenerate', [\App\Http\Controllers\Admin\SeoSettingsController::class , 'regenerateSitemap'])->name('sitemap.regenerate');

                    Route::resource('/redirects', \App\Http\Controllers\Admin\SeoRedirectController::class);

                    // Entity Specific SEO
                    Route::get('/pages', [\App\Http\Controllers\Admin\SeoManagerController::class , 'pages'])->name('pages');
                    Route::get('/services', [\App\Http\Controllers\Admin\SeoManagerController::class , 'services'])->name('services');
                    Route::get('/posts', [\App\Http\Controllers\Admin\SeoManagerController::class , 'posts'])->name('posts');
                    Route::get('/categories', [\App\Http\Controllers\Admin\SeoManagerController::class , 'categories'])->name('categories');

                Route::get('/edit/{type}/{id}', [\App\Http\Controllers\Admin\SeoManagerController::class , 'edit'])->name('edit');
                Route::post('/update/{type}/{id}', [\App\Http\Controllers\Admin\SeoManagerController::class , 'update'])->name('update');
            });
    });

    // Client Routes
    Route::group(['prefix' => 'client', 'as' => 'client.'], function () {
        Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class , 'index'])->name('dashboard');
            Route::get('/orders/running', [\App\Http\Controllers\User\OrderController::class, 'running'])->name('orders.running');
            Route::resource('/orders', \App\Http\Controllers\User\OrderController::class);
            Route::get('/orders/{order}/invoice', [\App\Http\Controllers\User\OrderController::class , 'invoice'])->name('orders.invoice');
            Route::post('/orders/{order}/submit-proof', [\App\Http\Controllers\User\OrderController::class , 'submitProof'])->name('orders.submit_proof');
            Route::resource('/projects', \App\Http\Controllers\User\ProjectController::class);
            Route::resource('/announcements', \App\Http\Controllers\User\AnnouncementController::class)->only(['index', 'show']);

            // In-Dashboard Ordering
            Route::get('/services', [\App\Http\Controllers\User\ServiceController::class , 'index'])->name('services.index');
            Route::get('/services/{service:slug}', [\App\Http\Controllers\User\ServiceController::class , 'show'])->name('services.show');
            Route::post('/services/{package}/checkout', [\App\Http\Controllers\User\ServiceController::class , 'checkout'])->name('services.checkout');

            // In-Dashboard Website Traffic
            Route::get('/website-traffic', [\App\Http\Controllers\User\WebsiteTrafficController::class , 'index'])->name('traffic.index');
            Route::get('/website-traffic/{service:slug}', [\App\Http\Controllers\User\ServiceController::class , 'show'])->name('traffic.show');
            Route::post('/website-traffic/{package}/checkout', [\App\Http\Controllers\User\ServiceController::class , 'checkout'])->name('traffic.checkout');

            // In-Dashboard Guest Posts
            Route::get('/guest-posts', [\App\Http\Controllers\User\GuestPostController::class , 'index'])->name('guest_posts.index');
            Route::get('/guest-posts/{guestPost}', [\App\Http\Controllers\User\GuestPostController::class , 'show'])->name('guest_posts.show');
            Route::post('/guest-posts/{guestPost}/favorite', [\App\Http\Controllers\User\GuestPostController::class , 'toggleFavorite'])->name('guest_posts.favorite');
            Route::post('/guest-posts/{guestPost}/checkout', [\App\Http\Controllers\User\GuestPostController::class , 'checkout'])->name('guest_posts.checkout');

            // Dynamic Campaign Routes (Client Dashboard)
            Route::group(['prefix' => 'campaigns/{type}', 'as' => 'campaigns.'], function () {
                    Route::get('/', [\App\Http\Controllers\User\CampaignController::class , 'index'])->name('index');
                    Route::get('/{service:slug}', [\App\Http\Controllers\User\CampaignController::class , 'show'])->name('show');
                    Route::post('/{package}/checkout', [\App\Http\Controllers\User\CampaignController::class , 'checkout'])->name('checkout');
                }
            );

            // Dedicated Link Building Routes (Client Dashboard)
            Route::get('/link-building', [\App\Http\Controllers\User\CampaignController::class , 'index'])->defaults('type', 'link-building')->name('link_building.index');
            Route::get('/link-building/{service:slug}', function (\App\Models\Service $service) {
                return app(\App\Http\Controllers\User\CampaignController::class)->show('link-building', $service);
            })->name('link_building.show');
            Route::post('/link-building/{package}/checkout', function (\Illuminate\Http\Request $request, \App\Models\Package $package) {
                return app(\App\Http\Controllers\User\CampaignController::class)->checkout($request, 'link-building', $package);
            })->name('link_building.checkout');

                // Menu Additions
                Route::get('/invoices', [\App\Http\Controllers\User\InvoiceController::class , 'index'])->name('invoices.index');
                Route::get('/invoices/{invoice}', [\App\Http\Controllers\User\InvoiceController::class , 'show'])->name('invoices.show');
                Route::post('/invoices/{invoice}/pay', [\App\Http\Controllers\User\InvoiceController::class, 'pay'])->name('invoices.pay');
                Route::get('/invoices/{invoice}/download', [\App\Http\Controllers\User\InvoiceController::class , 'download'])->name('invoices.download');

                Route::get('/support', [\App\Http\Controllers\User\SupportTicketController::class , 'index'])->name('support.index');
                Route::post('/support', [\App\Http\Controllers\User\SupportTicketController::class , 'store'])->name('support.store');
                Route::get('/faq', [\App\Http\Controllers\User\FaqController::class , 'index'])->name('faq.index');
                Route::get('/reports', [\App\Http\Controllers\User\ReportController::class , 'index'])->name('reports.index');
                Route::get('/affiliate', [\App\Http\Controllers\User\AffiliateController::class , 'index'])->name('affiliate.index');
                Route::get('/tools', [\App\Http\Controllers\User\ToolController::class , 'index'])->name('tools.index');
                Route::post('/tools/audit', [\App\Http\Controllers\User\ToolController::class , 'submitAudit'])->name('tools.audit');

                // Payments Hub
                Route::prefix('payments')->name('payments.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\User\PaymentController::class , 'index'])->name('index');
                    Route::get('/topup', [\App\Http\Controllers\User\PaymentController::class , 'topup'])->name('topup');
                    Route::post('/topup', [\App\Http\Controllers\User\PaymentController::class , 'processTopup'])->name('topup.process');
                    Route::post('/topup/manual', [\App\Http\Controllers\User\PaymentController::class , 'submitManualProof'])->name('topup.manual');
                });
        });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications/{id}/read', function ($id) {
            $notification = \App\Models\NotificationHub::findOrFail($id);
            if ($notification->user_id == auth()->id() || auth()->user()->is_admin) {
                $notification->update(['is_read' => true]);
            }
            return $notification->link ? redirect($notification->link) : back();
        }
        )->name('notifications.read');

        Route::post('/notifications/mark-all-read', function () {
            $query = \App\Models\NotificationHub::where('user_id', auth()->id());
            if (auth()->user()->is_admin) {
                $query->orWhereNull('user_id');
            }
            $query->update(['is_read' => true]);
            return back()->with('success', 'All notifications marked as read.');
        }
        )->name('notifications.markAllAsRead');
    });

require __DIR__ . '/auth.php';

Route::get('/mail-preview/topup', function () {
    return new \App\Mail\WalletTopupReceipt([
    'user_name' => 'John Doe',
    'amount' => 500.00,
    'payment_method' => 'stripe',
    'transaction_id' => 'ch_1234567890',
    'date' => now()->format('M d, Y h:i A')
    ]);
});
Route::get('/mail-preview/order', function () {
    return new \App\Mail\OrderConfirmation([
    'id' => 1024,
    'user_name' => 'Jane Smith',
    'title' => 'Service: Local SEO - Starter Package',
    'amount' => 150.00,
    'status' => 'Pending',
    'date' => now()->format('M d, Y h:i A')
    ]);
});
Route::get('/mail-preview/status', function () {
    return new \App\Mail\OrderStatusUpdated([
    'id' => 1024,
    'user_name' => 'Jane Smith',
    'title' => 'Service: Local SEO - Starter Package',
    'old_status' => 'Pending Requirements',
    'new_status' => 'Processing'
    ]);
});
Route::get('/mail-preview/message', function () {
    return new \App\Mail\NewMessageAlert([
    'recipient_name' => 'Jane Smith',
    'sender_name' => 'Admin Support',
    'message' => 'Hello Jane, we have started working on your keywords. Please provide us the GMB access.',
    'link' => route('client.orders.index') // Note: replaced specific show route to avoid missing id errors
    ]);
});
