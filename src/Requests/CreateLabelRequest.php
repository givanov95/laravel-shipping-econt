<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Requests;

use Givanov95\Shipping\Econt\Components\Address\Address;
use Givanov95\Shipping\Econt\Components\Clients\Client;
use Givanov95\Shipping\Econt\Enums\LabelMode;
use Givanov95\Shipping\Econt\Enums\ShipmentType;
use Givanov95\TypedHttp\Enums\HttpMethod;
use Givanov95\TypedHttp\Request;

class CreateLabelRequest extends Request
{
    public array $label;

    public string $mode;

    public function __construct(
        Client $senderClient,
        Client $receiverClient,
        Address $senderAddress,
        ?Address $receiverAddress,
        ?string $receiverOfficeCode,
        ShipmentType $shipmentType,
        LabelMode $mode,
        int $packCount,
        float $weight,
        string $shipmentDescription,
    ) {
        $this->label = [
            'senderClient'       => $senderClient->toArray(),
            'senderAddress'      => $senderAddress->toArray(),
            'receiverClient'     => $receiverClient->toArray(),
            'receiverAddress'    => $receiverAddress?->toArray() ?? null,
            'receiverOfficeCode' => $receiverOfficeCode,
            'packCount'          => $packCount,
            'shipmentType'       => $shipmentType->value,
            'weight'             => $weight,
        ];
        $this->mode = $mode->value;
    }

    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    public function endpoint(): string
    {
        return '/Shipments/LabelService.createLabel.json';
    }
}
