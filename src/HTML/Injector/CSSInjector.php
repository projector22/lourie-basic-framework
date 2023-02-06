<?php

namespace LBF\HTML\Injector;

use LBF\HTML\Injector\InjectorPositions;

trait CSSInjector {

    public array $injected_styles = [
        InjectorPositions::IN_HEAD        => [],
        InjectorPositions::TOP_OF_PAGE    => [],
        InjectorPositions::BOTTOM_OF_PAGE => [],
    ];

}