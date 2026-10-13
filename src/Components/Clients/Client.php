<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Components\Clients;

use Givanov95\Shipping\Econt\Components\BaseComponent;

class Client extends BaseComponent
{
    public string $name;

    public array $phones;

    public function __construct(string $name, array $phones)
    {
        $this->name = $name;
        $this->phones = $phones;
    }

    public function validate(): bool
    {
        return true;
    }
}
