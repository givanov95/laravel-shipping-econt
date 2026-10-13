<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Components;

interface ComponentInterface
{
    /**
     * @return array
     */
    public function toArray(): array;

    public function validate(): bool;
}
