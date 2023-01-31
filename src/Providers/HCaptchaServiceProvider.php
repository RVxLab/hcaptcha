<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use Scyllaly\HCaptcha\CaptchaVerifier;
use Scyllaly\HCaptcha\Facades\HCaptcha as HCaptchaFacade;
use Scyllaly\HCaptcha\HCaptcha;
use Scyllaly\HCaptcha\Views\Components\Script;
use Scyllaly\HCaptcha\Views\Components\Submit;
use Scyllaly\HCaptcha\Views\Components\Widget;
use Symfony\Component\HttpFoundation\Request;

final class HCaptchaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootConfig();

        $this->bootTranslations();

        $this->bootViewComponents();

        $this->bootViews();

        Validator::extend('HCaptcha', function ($attribute, $value) {
            /** @var Request $request */
            $request = request();

            return HCaptchaFacade::verifyResponse($value, $request->getClientIp());
        }, trans('hcaptcha::validation.invalid_captcha'));

        // @codeCoverageIgnoreStart
        if ($this->app->bound('form')) {
            $form = $this->app->make('form');

            $form::macro('HCaptcha', function (array $attributes = []) {
                return HCaptchaFacade::display($attributes);
            });
        }
        // @codeCoverageIgnoreEnd
    }

    private function bootConfig(): void
    {
        $path = __DIR__ . '/../../config/config.php';

        $this->mergeConfigFrom($path, 'HCaptcha');

        $this->publishes([
            $path => config_path('HCaptcha.php'),
        ], 'hcaptcha-config');
    }

    private function bootTranslations(): void
    {
        $sourcePath = __DIR__ . '/../../lang';

        $destPath = lang_path('vendor/hcaptcha');

        $this->loadTranslationsFrom($sourcePath, 'hcaptcha');

        $this->publishes([
            $sourcePath => $destPath,
        ], 'hcaptcha-lang');
    }

    private function bootViewComponents(): void
    {
        Blade::componentNamespace('Scyllaly\\HCaptcha\\Views\\Components', 'hcaptcha');

        Blade::components([
             Script::class,
             Widget::class,
             Submit::class,
        ]);
    }

    private function bootViews(): void
    {
        $viewPath = __DIR__ . '/../../resources/views';
        $publishPath = resource_path('views/vendor/hcaptcha');

        $this->loadViewsFrom($viewPath, 'hcaptcha');
        $this->publishes([
            $viewPath => $publishPath,
        ], 'hcaptcha-views');
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
