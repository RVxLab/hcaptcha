<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Tests\Feature\Views\Components;

use Illuminate\Support\Facades\Blade;
use Scyllaly\HCaptcha\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

final class WidgetTest extends TestCase
{
    use MatchesSnapshots;

    public function testCanRender(): void
    {
        $html = Blade::render('<x-hcaptcha::widget />');

        $this->assertMatchesHtmlSnapshot($html);
    }
}
