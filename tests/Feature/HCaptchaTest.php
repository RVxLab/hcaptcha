<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests\Feature;

use Scyllaly\HCaptcha\Facades\HCaptcha;
use Scyllaly\HCaptcha\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

final class HCaptchaTest extends TestCase
{
    use MatchesSnapshots;

    public function testCanDisplayWidget(): void
    {
        $this->assertMatchesHtmlSnapshot(HCaptcha::display());
    }

    public function testCanDisplayWidgetWithCustomAttributes(): void
    {
        $widget = HCaptcha::display([
            'class' => 'widget',
            'data-thing' => 'A thing',
            'role' => 'presentation',
        ]);

        $this->assertMatchesHtmlSnapshot($widget);
    }

    public function testInvisibleWidget(): void
    {
        $widget = HCaptcha::displaySubmit('my-form', 'Submit');

        $this->assertMatchesHtmlSnapshot($widget);
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
            '<script src="https://hcaptcha.com/1/api.js?render=explicit&amp;onload=onloadCallback" async defer></script>',
        ];

        yield 'With both set' => [
            [
                'lang' => 'nl',
                'hasCallback' => true,
                'onLoadClass' => 'onloadCallback',
            ],
            '<script src="https://hcaptcha.com/1/api.js?render=explicit&amp;onload=onloadCallback&amp;hl=nl" async defer></script>',
        ];
    }
}
