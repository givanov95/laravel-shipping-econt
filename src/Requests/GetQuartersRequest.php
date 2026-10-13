<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Requests;

use Givanov95\TypedHttp\Enums\HttpMethod;
use Givanov95\TypedHttp\Request;

class GetQuartersRequest extends Request
{
    public int $cityID;

    public function __construct(int $cityID)
    {
        $this->cityID = $cityID;
    }

    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    public function endpoint(): string
    {
        return '/Nomenclatures/NomenclaturesService.getQuarters.json';
    }
}
