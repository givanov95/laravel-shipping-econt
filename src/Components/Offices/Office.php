<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Components\Offices;

use DateTime;
use Givanov95\Shipping\Econt\Components\Address\Address;
use Givanov95\Shipping\Econt\Components\BaseComponent;

class Office extends BaseComponent
{
    public ?int $id;

    public ?string $code;

    public ?bool $isMPS;

    public ?bool $isAPS;

    public ?string $name;

    public ?string $nameEn;

    public ?array $phones;

    public ?array $emails;

    public ?Address $address;

    public ?string $info;

    public ?string $currency;

    public ?string $language;

    public ?DateTime $normalBusinessHoursFrom;

    public ?DateTime $normalBusinessHoursTo;

    public ?DateTime $halfDayBusinessHoursFrom;

    public ?DateTime $halfDayBusinessHoursTo;

    public ?DateTime $sundayBusinessHoursFrom;

    public ?DateTime $sundayBusinessHoursTo;

    public ?array $shipmentTypes;

    public ?string $partnerCode;

    public ?string $hubCode;

    public ?string $hubName;

    public ?string $hubNameEn;

    public ?bool $isDrive;

    public function __construct(
        ?int $id = null,
        ?string $code = null,
        ?bool $isMPS = null,
        ?bool $isAPS = null,
        ?string $name = null,
        ?string $nameEn = null,
        ?array $phones = null,
        ?array $emails = null,
        ?Address $address = null,
        ?string $info = null,
        ?string $currency = null,
        ?string $language = null,
        ?DateTime $normalBusinessHoursFrom = null,
        ?DateTime $normalBusinessHoursTo = null,
        ?DateTime $halfDayBusinessHoursFrom = null,
        ?DateTime $halfDayBusinessHoursTo = null,
        ?DateTime $sundayBusinessHoursFrom = null,
        ?DateTime $sundayBusinessHoursTo = null,
        ?array $shipmentTypes = null,
        ?string $partnerCode = null,
        ?string $hubCode = null,
        ?string $hubName = null,
        ?string $hubNameEn = null,
        ?bool $isDrive = null
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->isMPS = $isMPS;
        $this->isAPS = $isAPS;
        $this->name = $name;
        $this->nameEn = $nameEn;
        $this->phones = $phones;
        $this->emails = $emails;
        $this->address = $address;
        $this->info = $info;
        $this->currency = $currency;
        $this->language = $language;
        $this->normalBusinessHoursFrom = $normalBusinessHoursFrom;
        $this->normalBusinessHoursTo = $normalBusinessHoursTo;
        $this->halfDayBusinessHoursFrom = $halfDayBusinessHoursFrom;
        $this->halfDayBusinessHoursTo = $halfDayBusinessHoursTo;
        $this->sundayBusinessHoursFrom = $sundayBusinessHoursFrom;
        $this->sundayBusinessHoursTo = $sundayBusinessHoursTo;
        $this->shipmentTypes = $shipmentTypes;
        $this->partnerCode = $partnerCode;
        $this->hubCode = $hubCode;
        $this->hubName = $hubName;
        $this->hubNameEn = $hubNameEn;
        $this->isDrive = $isDrive;
    }

    public function validate(): bool
    {
        // Validation logic if required
        return true;
    }
}
