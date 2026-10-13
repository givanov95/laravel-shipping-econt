<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Components\Country;

class Country
{
    public ?int $id;

    public ?string $iso2Code;

    public ?string $iso3Code;

    public ?string $name;

    public ?string $nameEn;

    public ?bool $isEU;

    public function __construct(
        ?int $id = null,
        ?string $iso2Code = null,
        ?string $iso3Code = null,
        ?string $name = null,
        ?string $nameEn = null,
        ?bool $isEU = null
    ) {
        $this->id = $id;
        $this->iso2Code = $iso2Code;
        $this->iso3Code = $iso3Code;
        $this->name = $name;
        $this->nameEn = $nameEn;
        $this->isEU = $isEU;
    }
}
