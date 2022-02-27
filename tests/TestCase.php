<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Scyllaly\HCaptcha\HCaptchaServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param Application $app
     *
     * @return class-string<ServiceProvider>[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            HCaptchaServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set([
            'HCaptcha.sitekey' => 'HCaptchaSiteKey',
            'HCaptcha.secret' => 'HCaptchaSecret',
        ]);
    }
}
