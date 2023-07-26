<?php

namespace Pfrug\FileUpload;

use Illuminate\Support\ServiceProvider;

class FileUploadServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslations();
        $this->loadViews();

        $this->publishes([
            __DIR__.'/resources/assets/css' => public_path('vendor/fileupload/css'),
            __DIR__.'/resources/assets/js' => public_path('vendor/fileupload/js'),
            __DIR__.'/resources/assets/img' => public_path('vendor/fileupload/img'),
        ], 'fileupload-assets');
    }

    private function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'fileupload');

        $this->publishes(
            [
                __DIR__ . '/resources/lang' => resource_path('lang'),
            ],
            'fileupload-lang'
        );
    }

    private function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'fileupload');

        $this->publishes(
            [
                __DIR__ . '/resources/views' => resource_path('views/vendor/fileupload'),
            ],
            'fileupload-views'
        );
    }
}

//php artisan vendor:publish --tag=fileupload --provider="Pfrug\FileUpload\FileUploadServiceProvider"