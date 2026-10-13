<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Requests;

use Givanov95\Shipping\Econt\Components\City\City;
use Givanov95\TypedHttp\Enums\HttpMethod;
use Givanov95\TypedHttp\Request;

class GetStreetsRequest extends Request
{
    public int $cityID;

    public function __construct(City $city)
    {
        $this->cityID = $city->id;
    }

    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    public function endpoint(): string
    {
        return '/Nomenclatures/NomenclaturesService.getStreets.json';
    }
}
