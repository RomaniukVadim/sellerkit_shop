<?php

namespace App\Providers;
use App\Http\Controllers\Frontend;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('errors::404', function($view)
        {
			$header = (new Frontend())->header(false,false,false,true);
			$footer = (new Frontend())->footer();
            $view->with(compact('header','footer'));
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
