<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('minCount', function($attribute, $value, $parameters, $validator) {
            $validator->addReplacer('minCount',  function ($message, $attribute, $rule, $parameters) {
                return str_replace(':min_count', $parameters[0], $message);
            });
            return is_array($value) && count($value) >= $parameters[0];
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
