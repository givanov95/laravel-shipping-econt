<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Components\Address;

use Givanov95\Shipping\Econt\Components\BaseComponent;
use Givanov95\Shipping\Econt\Components\City\City;
use Givanov95\Shipping\Econt\Components\Street\Street;

class Address extends BaseComponent
{
    public string $countryIso2Code;

    public City $city;

    public Street $street;

    public string $number;

    public function __construct(string $countryIso2Code, City $city, Street $street, string $number)
    {
        $this->countryIso2Code = $countryIso2Code;
        $this->city = $city;
        $this->street = $street;
        $this->number = $number;
    }

    public function validate(): bool
    {
        return true;
    }

    /**
     * Get the value of city.
     *
     * @return City
     */
    public function getCity(): City
    {
        return $this->city;
    }

    /**
     * Get the value of street.
     *
     * @return Street
     */
    public function getStreet(): Street
    {
        return $this->street;
    }
}
