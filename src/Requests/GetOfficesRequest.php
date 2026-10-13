<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Requests;

use Givanov95\Shipping\Econt\Components\City\City;
use Givanov95\TypedHttp\Enums\HttpMethod;
use Givanov95\TypedHttp\Request;

class GetOfficesRequest extends Request
{
    public string $countryCode;

    public int $cityID;

    public function __construct(string $countryCode, City $city)
    {
        $this->countryCode = $countryCode;
        $this->cityID = $city->id;
    }

    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    public function endpoint(): string
    {
        return '/Nomenclatures/NomenclaturesService.getOffices.json';
    }
}
