<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Scyllaly\HCaptcha\Facades\HCaptcha as HCaptchaFacade;
use Symfony\Component\HttpFoundation\Request;

final class HCaptchaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootConfig();

        Validator::extend('HCaptcha', function ($attribute, $value) {
            /** @var Request $request */
            $request = request();

            return HCaptchaFacade::verifyResponse($value, $request->getClientIp());
        });

        if ($this->app->bound('form')) {
            $form = $this->app->make('form');

            $form::macro('HCaptcha', function (array $attributes = []) {
                return HCaptchaFacade::display($attributes);
            });
        }
    }

    protected function bootConfig(): void
    {
        $path = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom($path, 'HCaptcha');

        $this->publishes([
            $path => config_path('HCaptcha.php'),
        ]);
    }

    public function register(): void
    {
        $this->app->singleton(HCaptcha::class, function () {
            return new HCaptcha(
                config('HCaptcha.secret'),
                config('HCaptcha.sitekey'),
                config('HCaptcha.options', []),
            );
        });

        $this->app->alias(HCaptcha::class, 'HCaptcha');
    }
}
