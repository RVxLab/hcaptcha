<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests\Feature;

use Illuminate\Support\Facades\Validator;
use Scyllaly\HCaptcha\Facades\HCaptcha;
use Scyllaly\HCaptcha\Tests\TestCase;

final class ValidationTest extends TestCase
{
    public function testValidateSuccess(): void
    {
        HCaptcha::shouldReceive('verifyResponse')->andReturnTrue();

        $validator = Validator::make([
            'captcha' => 'ThisCaptchaIsValid',
        ], [
            'captcha' => 'required|HCaptcha',
        ]);

        self::assertTrue($validator->passes());
    }

    public function testValidateFailed(): void
    {
        HCaptcha::shouldReceive('verifyResponse')->andReturnFalse();

        $validator = Validator::make([
            'captcha' => 'ThisCaptchaIsNotValid',
        ], [
            'captcha' => 'required|HCaptcha',
        ]);

        self::assertFalse($validator->passes());

        self::assertSame('The CAPTCHA is invalid, please try again or contact the site admin.', $validator->errors()->first('captcha'));
    }
}
