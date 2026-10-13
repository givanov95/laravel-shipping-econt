<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt;

use Givanov95\Shipping\Econt\Requests\CreateLabelRequest;
use Givanov95\Shipping\Econt\Requests\GetCitiesRequest;
use Givanov95\Shipping\Econt\Requests\GetOfficesRequest;
use Givanov95\Shipping\Econt\Requests\GetQuartersRequest;
use Givanov95\Shipping\Econt\Requests\GetStreetsRequest;
use Givanov95\Shipping\Econt\Requests\ValidateAddressRequest;
use Givanov95\TypedHttp\Request;

/**
 * Sends the Econt requests and returns the decoded response (stdClass). Failures raise
 * Givanov95\TypedHttp\Exceptions\RequestException (4xx/5xx) or ConnectionException (no answer).
 */
class Econt
{
    public function __construct(private readonly ?EcontConnector $connector = null)
    {
    }

    public function validateAddress(ValidateAddressRequest $validateAddressRequest)
    {
        return $this->send($validateAddressRequest);
    }

    public function getCities(GetCitiesRequest $getCitiesRequest)
    {
        return $this->send($getCitiesRequest);
    }

    public function getOffices(GetOfficesRequest $getOfficesRequest)
    {
        return $this->send($getOfficesRequest);
    }

    public function getQuarters(GetQuartersRequest $getQuartersRequest)
    {
        return $this->send($getQuartersRequest);
    }

    public function getStreets(GetStreetsRequest $getStreetsRequest)
    {
        return $this->send($getStreetsRequest);
    }

    public function createLabel(CreateLabelRequest $createLabelRequest)
    {
        return $this->send($createLabelRequest);
    }

    private function send(Request $request): mixed
    {
        return ($this->connector ?? new EcontConnector())->send($request)->object();
    }
}
