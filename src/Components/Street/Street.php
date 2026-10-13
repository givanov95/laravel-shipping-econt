<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Components\Street;

use Givanov95\Shipping\Econt\Components\BaseComponent;
use Givanov95\Shipping\Econt\Components\City\City;

class Street extends BaseComponent
{
    public ?int $id;

    public ?City $city;

    public ?string $name;

    public ?string $nameEn;

    public function __construct(
        ?int $id = null,
        ?City $city = null,
        ?string $name = null,
        ?string $nameEn = null
    ) {
        $this->id = $id;
        $this->city = $city;
        $this->name = $name;
        $this->nameEn = $nameEn;
    }

    public function validate(): bool
    {
        return true;
    }
}
