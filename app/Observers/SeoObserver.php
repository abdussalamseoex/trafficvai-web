<?php

namespace App\Observers;

use App\Models\SeoRedirect;

class SeoObserver
{
    /**
     * Handle the model "updating" event.
     */
    public function updating($model): void
    {
        if ($model->isDirty('slug')) {
            $oldSlug = $model->getOriginal('slug');
            $newSlug = $model->slug;

            // Determine prefix based on model type
            $prefix = '';
            if ($model instanceof \App\Models\Service)
                $prefix = '/services/';
            if ($model instanceof \App\Models\Post)
                $prefix = '/blog/';
            if ($model instanceof \App\Models\Category)
                $prefix = '/services/category/';
            if ($model instanceof \App\Models\Page)
                $prefix = '/page/';

            $fromPath = $prefix . $oldSlug;
            $toPath = $prefix . $newSlug;

            // Create redirect if it doesn't exist
            if ($fromPath !== $toPath) {
                SeoRedirect::updateOrCreate(
                ['from_path' => $fromPath],
                ['to_path' => $toPath, 'type' => '301', 'is_active' => true]
                );
            }
        }
    }
}
