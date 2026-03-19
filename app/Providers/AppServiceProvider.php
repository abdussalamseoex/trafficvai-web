<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Observers for Notification System
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
        \App\Models\TopupRequest::observe(\App\Observers\TopupRequestObserver::class);
        \App\Models\OrderMessage::observe(\App\Observers\OrderMessageObserver::class);
        \App\Models\SeoMeta::observe(\App\Observers\SeoObserver::class);

        // Share Site Settings to ALL views globally
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                $settings = \App\Models\Setting::pluck('value', 'key');
                $view->with('global_settings', $settings);
            } catch (\Exception $e) {
                $view->with('global_settings', collect([]));
            }
        });

        \Illuminate\Support\Facades\View::composer('layouts.sidebar', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $data = [];

                if ($user->is_admin) {
                    $data['unreadOrdersCount'] = \App\Models\Order::where('is_read_admin', false)->count();

                    // Order Messages
                    $unreadOrderMessages = \App\Models\OrderMessage::where('is_read', false)
                        ->whereHas('user', function ($q) {
                        $q->where('is_admin', false);
                    }
                    )->count();

                    // Direct Messages
                    $unreadDirectMessages = \App\Models\DirectMessage::where('is_read', false)
                        ->whereHas('sender', function ($q) {
                        $q->where('is_admin', false);
                    }
                    )->count();

                    $data['unreadMessagesCount'] = $unreadOrderMessages + $unreadDirectMessages;
                    $data['unreadLeadsCount'] = \App\Models\Lead::where('status', 'pending')->count();
                }
                else {
                    // Client: Unread messages sent by admin to their orders
                    $unreadClientOrderMessages = \App\Models\OrderMessage::where('is_read', false)
                        ->whereHas('user', function ($q) {
                        $q->where('is_admin', true);
                    }
                    )
                        ->whereHas('order', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }
                    )->count();

                    // Client: Unread direct messages
                    $unreadClientDirectMessages = \App\Models\DirectMessage::where('is_read', false)
                        ->where('client_id', $user->id)
                        ->whereHas('sender', function ($q) {
                        $q->where('is_admin', true);
                    }
                    )->count();

                    $data['unreadClientMessagesCount'] = $unreadClientOrderMessages + $unreadClientDirectMessages;
                }

                $view->with($data);
            }
        });

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            // Priority 1: Use already shared SEO data if available
            $shared = \Illuminate\Support\Facades\View::getShared();
            if (isset($shared['seo']) && $shared['seo']) {
                // Already calculated and shared by a previous view in this request
                return;
            }

            try {
                $seoService = app(\App\Services\SeoService::class);
                $data = $view->getData();

                // Look for entity in current view data
                $entity = $data['service'] ?? $data['post'] ?? $data['page'] ?? $data['category'] ?? null;

                // Handle guest post sites specifically
                if (!$entity && isset($data['guestPost'])) {
                    $entity = $data['guestPost'];
                }

                // If we found an entity, calculate SEO and share it for the rest of the request
                if ($entity) {
                    $seo = $seoService->getMetadata($entity);
                    \Illuminate\Support\Facades\View::share('seo', $seo);
                } else {
                    // If no entity found, check if we're on the homepage or if we should use defaults
                    if (\request()->routeIs('home') || !isset($shared['seo'])) {
                        $seo = $seoService->getMetadata(null);
                        \Illuminate\Support\Facades\View::share('seo', $seo);
                    }
                }
            } catch (\Exception $e) {
                // Fallback for unexpected errors
                $fallback = [
                    'title' => config('app.name'),
                    'description' => '',
                    'keywords' => '',
                    'canonical' => 'https://trafficvai.com' . (request()->getPathInfo() === '/' ? '' : request()->getPathInfo()),
                    'robots' => 'index,follow',
                    'og' => [
                        'title' => config('app.name'),
                        'description' => '',
                        'image' => null,
                        'site_name' => config('app.name'),
                    ],
                    'twitter' => [
                        'card' => 'summary_large_image',
                        'site' => null,
                    ],
                    'schema' => null,
                    'scripts' => [
                        'header' => '',
                        'footer' => '',
                        'ga' => null,
                        'gsc' => null,
                    ]
                ];
                \Illuminate\Support\Facades\View::share('seo', $fallback);
            }
        });

        // SEO Observers
        \App\Models\Page::observe(\App\Observers\SeoObserver::class);
        \App\Models\Service::observe(\App\Observers\SeoObserver::class);
        \App\Models\Post::observe(\App\Observers\SeoObserver::class);
        \App\Models\Category::observe(\App\Observers\SeoObserver::class);

        \Illuminate\Support\Facades\View::composer('components.support-chat-popup', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $messages = \App\Models\DirectMessage::where('client_id', $user->id)
                    ->with(['sender'])
                    ->latest()
                    ->take(20)
                    ->get()
                    ->reverse()
                    ->values()
                    ->map(function ($msg) {
                    return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'is_self' => $msg->sender_id == auth()->id(),
                    'created_at' => $msg->created_at->diffForHumans(),
                    'is_read' => $msg->is_read,
                    'attachment_path' => $msg->attachment_path ? asset('storage/' . $msg->attachment_path) : null,
                    'attachment_name' => $msg->attachment_name,
                    ];
                }
                );
                $view->with('messages', $messages);
            }
            else {
                $view->with('messages', []);
            }
        });
    }
}
