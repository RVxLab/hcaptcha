<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Views\Components;

use Illuminate\View\Component;
use Scyllaly\HCaptcha\Facades\HCaptcha;

class Submit extends Component
{
    private string $formIdentifier;

    public function __construct(string $formIdentifier)
    {
        $this->formIdentifier = $formIdentifier;
    }

    public function render(): \Closure
    {
        return fn (array $data) => HCaptcha::displaySubmit(
            $this->formIdentifier,
            (string) $data['slot'],
            $data['attributes']->getAttributes(),
        );
    }
}
