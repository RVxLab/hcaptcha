<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests\Feature;

use Scyllaly\HCaptcha\HCaptcha;
use Scyllaly\HCaptcha\Tests\TestCase;

final class BindingTest extends TestCase
{
    public function testCanResolveHCaptchaWithClass(): void
    {
        $instance = $this->app->make(HCaptcha::class);

        self::assertInstanceOf(HCaptcha::class, $instance);
    }

    public function testCanResolveHCaptchaWithAlias(): void
    {
        $instance = $this->app->make('HCaptcha');

        self::assertInstanceOf(HCaptcha::class, $instance);
    }
}
