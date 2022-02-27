<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Facades;

use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method static string display(array $attributes = [])
 * @method static string displayWidget(array $attributes = [])
 * @method static string displaySubmit(string $formIdentifier, string $buttonText, array $attributes = [])
 * @method static string renderJs(?string $lang = null, bool $hasCallback = false, string $onLoadClass = 'onloadCallback')
 * @method static bool verifyResponse(string $response, ?string $clientIp = null)
 * @method static bool verifyRequest(Request $request)
 * @method static string getJsLink(?string $lang = null, bool $hasCallback = false, string $onLoadClass = 'onloadCallback')
 */
final class HCaptcha extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Scyllaly\HCaptcha\HCaptcha::class;
    }
}
