<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests\Feature\Views\Components;

use Illuminate\Support\Facades\Blade;
use Scyllaly\HCaptcha\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

final class SubmitTest extends TestCase
{
    use MatchesSnapshots;

    public function testCanRender(): void
    {
        $html = Blade::render('<x-hcaptcha::submit form-identifier="my-form">Submit</x-hcaptcha::submit>');

        $this->assertMatchesHtmlSnapshot($html);
    }
}
