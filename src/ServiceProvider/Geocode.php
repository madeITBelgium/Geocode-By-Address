<?php

namespace MadeITBelgium\Geocode\ServiceProvider;

use Illuminate\Support\ServiceProvider;

/**
 * Address Geo lookup Laravel PHP.
 *
 * @version    1.0.0
 *
 * @copyright  Copyright (c) 2018 Made I.T. (https://www.madeit.be)
 * @author     Tjebbe Lievens <tjebbe.lievens@madeit.be>
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-3.txt    LGPL
 */
class Geocode extends ServiceProvider
{
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/geocode.php' => config_path('geocode.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('geocode', function ($app) {
            $config = $app->make('config')->get('addressGeodata');

            return new \MadeITBelgium\Geocode\Geocode($config['type'], $config['key'], $config['client']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['geocode'];
    }
}
