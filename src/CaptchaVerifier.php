<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class CaptchaVerifier
{
    public const VERIFY_BASE_URL = 'https://hcaptcha.com';
    public const VERIFY_URL = '/siteverify';

    protected string $secret;

    protected ClientInterface $http;

    /** @var string[] */
    protected array $verifiedResponses = [];

    public function __construct(string $secret, ClientInterface $http)
    {
        $this->secret = $secret;
        $this->http = $http;
    }

    public function isValid(string $captchaResponse, ?string $clientIp = null): bool
    {
        if (empty($captchaResponse)) {
            return false;
        }

        if ($this->wasPreviouslyVerified($captchaResponse)) {
            return true;
        }

        $response = $this->http->sendRequest($this->makeRequest($captchaResponse, $clientIp));

        $responseBody = json_decode((string) $response->getBody(), true, 512, \JSON_THROW_ON_ERROR);

        $success = Arr::get($responseBody, 'success', false);

        if ($success) {
            $this->verifiedResponses[] = $captchaResponse;
        }

        return $success;
    }

    /** @internal Used for testing, do not use outside of testing */
    public function addVerifiedResponse(string $response): void
    {
        $this->verifiedResponses[] = $response;
    }

    protected function wasPreviouslyVerified(string $captchaResponse): bool
    {
        return \in_array($captchaResponse, $this->verifiedResponses, true);
    }

    protected function makeRequest(string $captchaResponse, ?string $clientIp = null): RequestInterface
    {
        $body = http_build_query([
            'secret' => $this->secret,
            'response' => $captchaResponse,
            'remoteip' => $clientIp,
        ]);

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
        ];

        return new Request('post', self::VERIFY_URL, $headers, $body);
    }
}
