<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;

class HCaptcha
{
    public const CLIENT_API = 'https://hcaptcha.com/1/api.js';
    public const VERIFY_URL = 'https://hcaptcha.com/siteverify';

    protected string $secret;

    protected string $siteKey;

    protected Client $http;

    /** @var string[] */
    protected array $verifiedResponses = [];

    /** @param mixed[] $options */
    public function __construct(string $secret, string $siteKey, array $options = [])
    {
        $this->secret = $secret;
        $this->siteKey = $siteKey;
        $this->http = new Client($options);
    }

    /**
     * Render HTML captcha.
     *
     * @param mixed[] $attributes
     */
    public function display(array $attributes = []): string
    {
        $attributes = $this->prepareAttributes($attributes);

        return '<div' . $this->buildAttributes($attributes) . '></div>';
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
            $javascript = sprintf(
                '<script>function %s(){document.getElementById("%s").submit();}</script>',
                $functionName,
                $formIdentifier,
            );
        }

        $attributes = $this->prepareAttributes($attributes);

        $button = sprintf('<button%s><span>%s</span></button>', $this->buildAttributes($attributes), $buttonText);

        return $button . $javascript;
    }

    /** Render js source */
    public function renderJs(?string $lang = null, bool $hasCallback = false, string $onLoadClass = 'onloadCallBack'): string
    {
        return '<script src="' . $this->getJsLink($lang, $hasCallback, $onLoadClass) . '" async defer></script>' . "\n";
    }

    /** Verify hCaptcha response. */
    public function verifyResponse(string $response, ?string $clientIp = null): bool
    {
        if (empty($response)) {
            return false;
        }

        // Return true if response already verfied before.
        if (\in_array($response, $this->verifiedResponses)) {
            return true;
        }

        $verifyResponse = $this->sendRequestVerify([
            'secret' => $this->secret,
            'response' => $response,
            'remoteip' => $clientIp,
        ]);

        if (isset($verifyResponse['success']) && $verifyResponse['success'] === true) {
            // A response can only be verified once from hCaptcha, so we need to
            // cache it to make it work in case we want to verify it multiple times.
            $this->verifiedResponses[] = $response;

            return true;
        }

        return false;
    }

    /** Verify hCaptcha response by Symfony Request. */
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
     * Send verify request.
     *
     * @param mixed[] $query
     *
     * @return mixed[]
     */
    protected function sendRequestVerify(array $query = [])
    {
        $response = $this->http->request('POST', static::VERIFY_URL, [
            'form_params' => $query,
        ]);

        return json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);
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
        $html = [];

        foreach ($attributes as $key => $value) {
            $html[] = $key . '="' . $value . '"';
        }

        return \count($html) ? ' ' . implode(' ', $html) : '';
    }
}
