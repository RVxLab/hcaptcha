<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests\Feature\Views\Components;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\Blade;
use Scyllaly\HCaptcha\Tests\TestCase;

final class ScriptTest extends TestCase
{
    use InteractsWithViews;

    public function testCanRender(): void
    {
        $html = Blade::render('<x-hcaptcha::script />');

        self::assertSame('<script src="https://hcaptcha.com/1/api.js" async defer></script>', trim($html));
    }

    public function testCanRenderWithAppLocaleSet(): void
    {
        app()->setLocale('nl');

        $html = Blade::render('<x-hcaptcha::script use-app-locale />');

        self::assertSame('<script src="https://hcaptcha.com/1/api.js?hl=nl" async defer></script>', trim($html));
    }
}
