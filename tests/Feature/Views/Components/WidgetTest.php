<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests\Feature\Views\Components;

use Illuminate\Support\Facades\Blade;
use Scyllaly\HCaptcha\Tests\TestCase;

final class WidgetTest extends TestCase
{
    public function testCanRender(): void
    {
        $html = Blade::render('<x-hcaptcha::widget />');

        self::assertSame('<div data-sitekey="HCaptchaSiteKey" class="h-captcha"></div>', trim($html));
    }
}
