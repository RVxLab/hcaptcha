<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;

class HCaptcha
{
    public const CLIENT_API = 'https://hcaptcha.com/1/api.js';

    protected string $siteKey;
    private CaptchaVerifier $captchaVerifier;

    public function __construct(string $siteKey, CaptchaVerifier $captchaVerifier)
    {
        $this->siteKey = $siteKey;
        $this->captchaVerifier = $captchaVerifier;
    }

    /**
     * Render HTML captcha.
     *
     * @param mixed[] $attributes
     */
    public function display(array $attributes = []): string
    {
        $attributes = $this->prepareAttributes($attributes);

        return view('hcaptcha::widget', [
            'attributes' => $this->buildAttributes($attributes),
        ])->render();
    }

    /**
     * @codeCoverageIgnore
     *
     * @see display()
     *
     * @param mixed[] $attributes
     */
    public function displayWidget(array $attributes = []): string
    {
        return $this->display($attributes);
    }

    /**
     * Display an Invisible hCaptcha by embedding a callback into a form submit button.
     *
     * @param string $formIdentifier the html ID of the form that should be submitted.
     * @param string $buttonText           the text inside the form button
     * @param mixed[]  $attributes     array of additional html elements
     */
    public function displaySubmit(string $formIdentifier, string $buttonText = 'submit', array $attributes = []): string
    {
        $javascript = '';
        if (! isset($attributes['data-callback'])) {
            $functionName = sprintf('on%sSubmit', Str::of($formIdentifier)->title()->replace(['-', '=', '\'', '"', '<', '>', '`'], ''));
            $attributes['data-callback'] = $functionName;

            $javascript = view('hcaptcha::default-submit-callback', [
                'formIdentifier' => $formIdentifier,
                'functionName' => $functionName,
            ])->render();
        }

        $attributes = $this->prepareAttributes($attributes);

        $button = view('hcaptcha::submit', [
            'attributes' => $this->buildAttributes($attributes),
            'text' => $buttonText,
        ])->render();

        return $button . $javascript;
    }

    /** Render js source */
    public function renderJs(?string $lang = null, bool $hasCallback = false, string $onLoadClass = 'onloadCallBack'): string
    {
        return view('hcaptcha::script', [
            'url' => $this->getJsLink($lang, $hasCallback, $onLoadClass),
        ])->render();
    }

    /**
     * Verify hCaptcha response.
     *
     * @codeCoverageIgnore
     */
    public function verifyResponse(string $response, ?string $clientIp = null): bool
    {
        return $this->captchaVerifier->isValid($response, $clientIp);
    }

    /**
     * Verify hCaptcha response by Symfony Request.
     *
     * @codeCoverageIgnore
     */
    public function verifyRequest(Request $request): bool
    {
        return $this->verifyResponse(
            $request->get('h-captcha-response'),
            $request->getClientIp(),
        );
    }

    /** Get hCaptcha js link. */
    public function getJsLink(?string $lang = null, bool $hasCallback = false, string $onLoadClass = 'onloadCallBack'): string
    {
        $params = [];

        if ($hasCallback) {
            $params['render'] = 'explicit';
            $params['onload'] = $onLoadClass;
        }

        if ($lang) {
            $params['hl'] = $lang;
        }

        if (empty($params)) {
            return self::CLIENT_API;
        }

        return self::CLIENT_API . '?' . http_build_query($params);
    }

    /**
     * Prepare HTML attributes and assure that the correct classes and attributes for captcha are inserted.
     *
     * @param mixed[] $attributes
     *
     * @return string[]
     */
    protected function prepareAttributes(array $attributes): array
    {
        $attributes['data-sitekey'] = $this->siteKey;

        if (! isset($attributes['class'])) {
            $attributes['class'] = '';
        }

        $attributes['class'] = trim('h-captcha ' . $attributes['class']);

        return $attributes;
    }

    /**
     * Build HTML attributes.
     *
     * @param mixed[] $attributes
     *
     * @return string
     */
    protected function buildAttributes(array $attributes): string
    {
        $htmlAttributesAsString = [];

        foreach ($attributes as $key => $value) {
            $htmlAttributesAsString[] = sprintf('%s="%s"', $key, $value);
        }

        return implode(' ', $htmlAttributesAsString);
    }
}
