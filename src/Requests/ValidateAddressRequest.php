<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Requests;

use Givanov95\Shipping\Econt\Components\Address\Address;
use Givanov95\TypedHttp\Enums\HttpMethod;
use Givanov95\TypedHttp\Request;

class ValidateAddressRequest extends Request
{
    public array $address;

    public function __construct(Address $address)
    {
        $this->address = [
            'city'   => $address->city->toArray(),
            'street' => $address->street,
            'num'    => $address->number,
        ];
    }

    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    public function endpoint(): string
    {
        return '/Nomenclatures/AddressService.validateAddress.json';
    }
}
