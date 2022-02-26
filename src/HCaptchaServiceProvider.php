<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha;

use Illuminate\Support\ServiceProvider;

final class HCaptchaServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot(): void
    {
        $app = $this->app;

        $this->bootConfig();

        $app['validator']->extend('HCaptcha', function ($attribute, $value) use ($app) {
            return $app['HCaptcha']->verifyResponse($value, $app['request']->getClientIp());
        });

        if ($app->bound('form')) {
            $app['form']->macro('HCaptcha', function ($attributes = []) use ($app) {
                return $app['HCaptcha']->display($attributes, $app->getLocale());
            });
        }
    }

    protected function bootConfig(): void
    {
        $path = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom($path, 'HCaptcha');

        if (\function_exists('config_path')) {
            $this->publishes([$path => config_path('HCaptcha.php')]);
        }
    }

    public function register(): void
    {
        $this->app->singleton('HCaptcha', function ($app) {
            return new HCaptcha(
                $app['config']['HCaptcha.secret'],
                $app['config']['HCaptcha.sitekey'],
                $app['config']['HCaptcha.options'],
            );
        });

        $this->app->alias('HCaptcha', HCaptcha::class);
    }

    /** @return string[] */
    public function provides(): array
    {
        return ['HCaptcha'];
    }
}
