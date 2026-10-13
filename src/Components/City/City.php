<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Components\City;

use Givanov95\Shipping\Econt\Components\BaseComponent;
use Givanov95\Shipping\Econt\Components\Country\Country;
use Givanov95\Shipping\Econt\Components\GeoLocation\GeoLocation;

class City extends BaseComponent
{
    public int $id;

    public ?Country $country;

    public ?string $postCode;

    public ?string $name;

    public ?string $nameEn;

    public ?string $regionName;

    public ?string $regionNameEn;

    public ?string $phoneCode;

    public ?GeoLocation $location;

    public ?bool $expressCityDeliveries;

    public ?bool $monday;

    public ?bool $tuesday;

    public ?bool $wednesday;

    public ?bool $thursday;

    public ?bool $friday;

    public ?bool $saturday;

    public ?bool $sunday;

    public ?int $serviceDays;

    public ?int $zoneId;

    public ?string $zoneName;

    public ?string $zoneNameEn;

    public ?array $servingOffices;

    public function __construct(
        int $id,
        ?Country $country = null,
        ?string $postCode = null,
        ?string $name = null,
        ?string $nameEn = null,
        ?string $regionName = null,
        ?string $regionNameEn = null,
        ?string $phoneCode = null,
        ?GeoLocation $location = null,
        ?bool $expressCityDeliveries = null,
        ?bool $monday = null,
        ?bool $tuesday = null,
        ?bool $wednesday = null,
        ?bool $thursday = null,
        ?bool $friday = null,
        ?bool $saturday = null,
        ?bool $sunday = null,
        ?int $serviceDays = null,
        ?int $zoneId = null,
        ?string $zoneName = null,
        ?string $zoneNameEn = null,
        ?array $servingOffices = null
    ) {
        $this->id = $id;
        $this->country = $country;
        $this->postCode = $postCode;
        $this->name = $name;
        $this->nameEn = $nameEn;
        $this->regionName = $regionName;
        $this->regionNameEn = $regionNameEn;
        $this->phoneCode = $phoneCode;
        $this->location = $location;
        $this->expressCityDeliveries = $expressCityDeliveries;
        $this->monday = $monday;
        $this->tuesday = $tuesday;
        $this->wednesday = $wednesday;
        $this->thursday = $thursday;
        $this->friday = $friday;
        $this->saturday = $saturday;
        $this->sunday = $sunday;
        $this->serviceDays = $serviceDays;
        $this->zoneId = $zoneId;
        $this->zoneName = $zoneName;
        $this->zoneNameEn = $zoneNameEn;
        $this->servingOffices = $servingOffices;
    }

    /**
     * Validate the City instance.
     */
    public function validate(): bool
    {
        // At least an ID or (name + postCode + country if outside Bulgaria)
        if ($this->id) {
            return true;
        }

        if ($this->name && $this->postCode) {
            if ($this->isInBulgaria() || $this->country) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the City is in Bulgaria.
     */
    private function isInBulgaria(): bool
    {
        return $this->country?->code2 === 'BG';
    }
}
