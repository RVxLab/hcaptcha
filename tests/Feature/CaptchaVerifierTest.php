<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Scyllaly\HCaptcha\CaptchaVerifier;
use Scyllaly\HCaptcha\Tests\TestCase;

final class CaptchaVerifierTest extends TestCase
{
    public function testEmptyResponse(): void
    {
        self::assertFalse($this->makeVerifier()->isValid(''));
    }

    public function testAlreadyVerified(): void
    {
        $verifier = $this->makeVerifier();

        $verifiedResponse = 'ThisIsAVerifiedResponse';

        $verifier->addVerifiedResponse($verifiedResponse);

        self::assertTrue($verifier->isValid($verifiedResponse));
    }

    public function testVerifiedSuccessfully(): void
    {
        $this->app
            ->when(CaptchaVerifier::class)
            ->needs(ClientInterface::class)
            ->give(fn () => $this->makeMockClient(true));

        self::assertTrue($this->makeVerifier()->isValid('Test'));
    }

    public function testVerificationFailed(): void
    {
        $this->app
            ->when(CaptchaVerifier::class)
            ->needs(ClientInterface::class)
            ->give(fn () => $this->makeMockClient(false));

        self::assertFalse($this->makeVerifier()->isValid('Test'));
    }

    private function makeVerifier(): CaptchaVerifier
    {
        return $this->app->make(CaptchaVerifier::class);
    }

    private function makeMockClient(bool $isSuccessful): Client
    {
        $mockHandler = new MockHandler([
            new Response(200, [], json_encode([
                'success' => $isSuccessful,
                'challenge_ts' => now()->toIso8601ZuluString(),
                'hostname' => 'localhost',
            ], \JSON_THROW_ON_ERROR)),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        return new Client([
            'base_uri' => CaptchaVerifier::VERIFY_BASE_URL,
            'timeout' => 15,
            'handler' => $handlerStack,
        ]);
    }
}
