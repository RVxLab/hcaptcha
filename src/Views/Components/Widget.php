<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Views\Components;

use Illuminate\View\Component;
use Scyllaly\HCaptcha\Facades\HCaptcha;

class Widget extends Component
{
    public function render(): \Closure
    {
        return static fn (array $data) => HCaptcha::display($data['attributes']->getAttributes());
    }
}
