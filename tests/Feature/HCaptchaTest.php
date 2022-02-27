<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests\Feature;

use Scyllaly\HCaptcha\Facades\HCaptcha;
use Scyllaly\HCaptcha\Tests\TestCase;

final class HCaptchaTest extends TestCase
{
    public function testCanDisplayWidget(): void
    {
        $expectedWidget = trim(<<<HTML
            <div data-sitekey="HCaptchaSiteKey" class="h-captcha"></div>
        HTML);

        $widget = HCaptcha::display();

        self::assertSame($expectedWidget, $widget);
    }

    public function testCanDisplayWidgetWithCustomAttributes(): void
    {
        $expectedWidget = trim(<<<HTML
            <div class="h-captcha widget" data-thing="A thing" role="presentation" data-sitekey="HCaptchaSiteKey"></div>
        HTML);

        $widget = HCaptcha::display([
            'class' => 'widget',
            'data-thing' => 'A thing',
            'role' => 'presentation',
        ]);

        self::assertSame($expectedWidget, $widget);
    }

    public function testInvisibleWidget(): void
    {
        $expectedWidget = trim(<<<HTML
            <button data-callback="onMyFormSubmit" data-sitekey="HCaptchaSiteKey" class="h-captcha"><span>Submit</span></button><script>function onMyFormSubmit(){document.getElementById("my-form").submit();}</script>
        HTML);

        $widget = HCaptcha::displaySubmit('my-form', 'Submit');

        self::assertSame($expectedWidget, $widget);
    }

    /** @dataProvider jsLinkProvider */
    public function testJsLink(array $params, string $expectedScriptTag): void
    {
        $scriptTag = HCaptcha::renderJs(...array_values($params));

        self::assertSame(trim($expectedScriptTag), trim($scriptTag));
    }

    public function jsLinkProvider(): iterable
    {
        yield 'Default values' => [
            [
                'lang' => null,
                'hasCallback' => false,
                'onLoadClass' => 'onloadCallback',
            ],
            '<script src="https://hcaptcha.com/1/api.js" async defer></script>',
        ];

        yield 'With lang set' => [
            [
                'lang' => 'nl',
                'hasCallback' => false,
                'onLoadClass' => 'onloadCallback',
            ],
            '<script src="https://hcaptcha.com/1/api.js?hl=nl" async defer></script>',
        ];

        yield 'With callback set' => [
            [
                'lang' => null,
                'hasCallback' => true,
                'onLoadClass' => 'onloadCallback',
            ],
            '<script src="https://hcaptcha.com/1/api.js?render=explicit&onload=onloadCallback" async defer></script>',
        ];

        yield 'With both set' => [
            [
                'lang' => 'nl',
                'hasCallback' => true,
                'onLoadClass' => 'onloadCallback',
            ],
            '<script src="https://hcaptcha.com/1/api.js?render=explicit&onload=onloadCallback&hl=nl" async defer></script>',
        ];
    }
}
