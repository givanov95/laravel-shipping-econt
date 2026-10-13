<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Shipments;

use Brick\Money\Money;
use Givanov95\Shipping\Econt\Components\Address\Address;
use Givanov95\Shipping\Econt\Components\City\City;
use Givanov95\Shipping\Econt\Components\Clients\Client;
use Givanov95\Shipping\Econt\Components\Country\Country;
use Givanov95\Shipping\Econt\Components\Street\Street;
use Givanov95\Shipping\Econt\Econt;
use Givanov95\Shipping\Econt\Enums\LabelMode;
use Givanov95\Shipping\Econt\Enums\ShipmentType;
use Givanov95\Shipping\Econt\Requests\CreateLabelRequest;
use Givanov95\TypedHttp\Request;

class EcontAddressShipment
{
    private string $senderName;

    private array $senderPhones;

    private string $senderCountryIso2Code;

    private int $senderCityId;

    private string $senderStreet;

    private string $senderStreetNum;

    private string $receiverName;

    private array $receiverPhones;

    private string $receiverCountryIso2Code;

    private int $receiverCityId;

    private string $receiverStreet;

    private string $receiverStreetNum;

    private float $packageWeight;

    private string $description;

    public function __construct(
        string $senderName,
        array $senderPhones,
        string $senderCountryIso2Code,
        int $senderCityId,
        string $senderStreet,
        string $senderStreetNum,
        string $receiverName,
        array $receiverPhones,
        string $receiverCountryIso2Code,
        int $receiverCityId,
        string $receiverStreet,
        string $receiverStreetNum,
        float $packageWeight,
        string $description,
    ) {
        $this->senderName = $senderName;
        $this->senderPhones = $senderPhones;
        $this->senderCountryIso2Code = $senderCountryIso2Code;
        $this->senderCityId = $senderCityId;
        $this->senderStreet = $senderStreet;
        $this->senderStreetNum = $senderStreetNum;

        $this->receiverName = $receiverName;
        $this->receiverPhones = $receiverPhones;
        $this->receiverCountryIso2Code = $receiverCountryIso2Code;
        $this->receiverCityId = $receiverCityId;
        $this->receiverStreet = $receiverStreet;
        $this->receiverStreetNum = $receiverStreetNum;

        $this->packageWeight = $packageWeight;

        $this->description = $description;
    }

    public function getLabelRequest(): Request
    {
        $senderClient = new Client(
            name: $this->senderName,
            phones: $this->senderPhones
        );

        $receiverClient = new Client(
            name: $this->receiverName,
            phones: $this->receiverPhones
        );

        $senderCountry = new Country(iso2Code: $this->senderCountryIso2Code);
        $senderCityId = new City(id: $this->senderCityId, country: $senderCountry);
        $senderStreet = new Street(city: $senderCityId, name: $this->senderStreet);
        $senderAddress = new Address(
            countryIso2Code: $this->senderCountryIso2Code,
            city: $senderCityId,
            street: $senderStreet,
            number: $this->senderStreetNum
        );

        $receiverCountry = new Country(iso2Code: $this->receiverCountryIso2Code);
        $receiverCityId = new City(id: $this->receiverCityId, country: $receiverCountry);
        $receiverStreet = new Street(city: $receiverCityId, name: $this->receiverStreet);
        $receiverAddress = new Address(
            countryIso2Code: $this->receiverCountryIso2Code,
            city: $receiverCityId,
            street: $receiverStreet,
            number: $this->receiverStreetNum
        );

        return new CreateLabelRequest(
            senderClient: $senderClient,
            receiverClient: $receiverClient,
            senderAddress: $senderAddress,
            receiverAddress: $receiverAddress,
            receiverOfficeCode: null,
            shipmentType: ShipmentType::Pack,
            mode: LabelMode::Calculate,
            packCount: 1,
            weight: $this->packageWeight,
            shipmentDescription: $this->description
        );
    }

    public function getPrice(): ?Money
    {
        $label = (new Econt())->createLabel($this->getLabelRequest())?->label;

        if (! $label) {
            return null;
        }

        $currency = $label->currency;
        $amount = $label->totalPrice;

        return Money::of((string) $amount, $currency);
    }

    public function toArray(): array
    {
        $price = $this->getPrice();

        return [
            'amount'   => $price->getAmount(),
            'currency' => $price->getCurrency()->getCurrencyCode(),
        ];
    }
}
