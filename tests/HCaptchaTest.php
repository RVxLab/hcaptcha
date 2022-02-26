<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests;

use Scyllaly\HCaptcha\HCaptcha;

final class HCaptchaTest extends TestCase
{
    /** @var HCaptchaTest */
    private $captcha;

    protected function setUp(): void
    {
        parent::setUp();

        $this->captcha = new hCaptcha('{secret-key}', '{site-key}');
    }

    public function testJsLink(): void
    {
        $this->assertInstanceOf(HCaptcha::class, $this->captcha);

        $simple = '<script src="https://hcaptcha.com/1/api.js?" async defer></script>' . "\n";
        $withLang = '<script src="https://hcaptcha.com/1/api.js?hl=vi" async defer></script>' . "\n";
        $withCallback = '<script src="https://hcaptcha.com/1/api.js?render=explicit&onload=myOnloadCallback" async defer></script>' . "\n";

        $this->assertEquals($simple, $this->captcha->renderJs());
        $this->assertEquals($withLang, $this->captcha->renderJs('vi'));
        $this->assertEquals($withCallback, $this->captcha->renderJs(null, true, 'myOnloadCallback'));
    }

    public function testDisplay(): void
    {
        $this->assertInstanceOf(HCaptcha::class, $this->captcha);

        $simple = '<div data-sitekey="{site-key}" class="h-captcha"></div>';
        $withAttrs = '<div data-theme="light" data-sitekey="{site-key}" class="h-captcha"></div>';

        $this->assertEquals($simple, $this->captcha->display());
        $this->assertEquals($withAttrs, $this->captcha->display(['data-theme' => 'light']));
    }

    public function testDisplaySubmit(): void
    {
        $this->assertInstanceOf(HCaptcha::class, $this->captcha);

        $javascript = '<script>function onSubmittest(){document.getElementById("test").submit();}</script>';
        $simple = '<button data-callback="onSubmittest" data-sitekey="{site-key}" class="h-captcha"><span>submit</span></button>';
        $withAttrs = '<button data-theme="light" class="h-captcha 123" data-callback="onSubmittest" data-sitekey="{site-key}"><span>submit123</span></button>';

        $this->assertEquals($simple . $javascript, $this->captcha->displaySubmit('test'));
        $withAttrsResult = $this->captcha->displaySubmit('test', 'submit123', ['data-theme' => 'light', 'class' => '123']);
        $this->assertEquals($withAttrs . $javascript, $withAttrsResult);
    }

    public function testDisplaySubmitWithCustomCallback(): void
    {
        $this->assertInstanceOf(HCaptcha::class, $this->captcha);

        $withAttrs = '<button data-theme="light" class="h-captcha 123" data-callback="onSubmitCustomCallback" data-sitekey="{site-key}"><span>submit123</span></button>';

        $withAttrsResult = $this->captcha->displaySubmit('test-custom', 'submit123', ['data-theme' => 'light', 'class' => '123', 'data-callback' => 'onSubmitCustomCallback']);
        $this->assertEquals($withAttrs, $withAttrsResult);
    }
}
