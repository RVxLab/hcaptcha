<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use Scyllaly\HCaptcha\CaptchaVerifier;
use Scyllaly\HCaptcha\Facades\HCaptcha as HCaptchaFacade;
use Scyllaly\HCaptcha\HCaptcha;
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
        }, 'The CAPTCHA is invalid, please try again or contact the site admin.');

        // @codeCoverageIgnoreStart
        if ($this->app->bound('form')) {
            $form = $this->app->make('form');

            $form::macro('HCaptcha', function (array $attributes = []) {
                return HCaptchaFacade::display($attributes);
            });
        }
        // @codeCoverageIgnoreEnd
    }

    protected function bootConfig(): void
    {
        $path = __DIR__ . '/../../config/config.php';

        $this->mergeConfigFrom($path, 'HCaptcha');

        $this->publishes([
            $path => config_path('HCaptcha.php'),
        ]);
    }

    public function register(): void
    {
        $this->app
            ->when(HCaptcha::class)
            ->needs('$siteKey')
            ->giveConfig('HCaptcha.sitekey');

        $this->app
            ->when(CaptchaVerifier::class)
            ->needs('$secret')
            ->giveConfig('HCaptcha.secret');

        $this->app
            ->when(CaptchaVerifier::class)
            ->needs(ClientInterface::class)
            ->give(function () {
                return new Client([
                    'base_uri' => CaptchaVerifier::VERIFY_BASE_URL,
                    'timeout' => 30,
                ]);
            });

        $this->app->alias(HCaptcha::class, 'HCaptcha');
    }
}
