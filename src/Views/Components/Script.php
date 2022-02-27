<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Views\Components;

use Illuminate\View\Component;
use Scyllaly\HCaptcha\Facades\HCaptcha;

class Script extends Component
{
    private ?string $lang;
    private bool $hasCallback;
    private string $onLoadClass;

    public function __construct(
        ?string $lang = null,
        bool $useAppLocale = false,
        bool $hasCallback = false,
        string $onLoadClass = 'onloadCallback'
    ) {
        $this->lang = $useAppLocale ? app()->getLocale() : $lang;
        $this->hasCallback = $hasCallback;
        $this->onLoadClass = $onLoadClass;
    }

    public function render(): string
    {
        return HCaptcha::renderJs($this->lang, $this->hasCallback, $this->onLoadClass);
    }
}
