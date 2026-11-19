<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use \App\Models\Blacklist;

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

        Validator::extend('kth_email', function($attribute, $value, $parameters, $validator) {
            return preg_match("/^.{1,8}@kth\.se$/", $value);
        });

        Validator::extend('not_blacklisted', function($attribute, $value, $parameters, $validator) {
            return !Blacklist::isBlacklisted($value);
        });

        Validator::extend('is_blacklisted', function($attribute, $value, $parameters, $validator) {
            return Blacklist::isBlacklisted($value);
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
