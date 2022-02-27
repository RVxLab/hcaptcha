<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests\Feature\Views\Components;

use Illuminate\Support\Facades\Blade;
use Scyllaly\HCaptcha\Tests\TestCase;

final class SubmitTest extends TestCase
{
    public function testCanRender(): void
    {
        $html = Blade::render('<x-hcaptcha::submit form-identifier="my-form">Submit</x-hcaptcha::submit>');

        $expectedHtml = trim(<<<HTML
            <button data-callback="onMyFormSubmit" data-sitekey="HCaptchaSiteKey" class="h-captcha"><span>Submit</span></button><script>function onMyFormSubmit(){document.getElementById("my-form").submit();}</script>
        HTML);

        self::assertSame($expectedHtml, trim($html));
    }
}
