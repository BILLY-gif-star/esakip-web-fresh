<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ══ GLOBAL FILE SIZE LIMIT 5MB ══
        // Berlaku untuk SEMUA upload di seluruh aplikasi
        // Tidak perlu ubah controller satu per satu
        Validator::extendImplicit('file_max_5mb', function ($attribute, $value, $parameters, $validator) {
            if ($value instanceof UploadedFile) {
                if ($value->getSize() > 5 * 1024 * 1024) {
                    $validator->errors()->add($attribute, 'Ukuran file maksimal 5MB.');
                    return false;
                }
            }
            return true;
        });
    }
}