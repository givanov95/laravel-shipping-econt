<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt;

use Brick\Money\Money;
use Givanov95\Shipping\Contracts\DeliveryMethod;
use Givanov95\Shipping\Contracts\Parcel;
use Givanov95\Shipping\Contracts\PlaceDirectory;
use Givanov95\Shipping\Destination;
use Givanov95\Shipping\DestinationType;
use Givanov95\Shipping\Econt\Components\City\City;
use Givanov95\Shipping\Place;
use Givanov95\Shipping\Point;
use Givanov95\Shipping\Street;

/**
 * Econt behind the contract: to one of its offices, or to an address in a city it knows by id.
 */
class EcontMethod implements DeliveryMethod, PlaceDirectory
{
    /** Econt is asked for the cities of one country. */
    private const COUNTRY = 'BG';

    private const SEARCH_LIMIT = 20;

    public function __construct(private readonly EcontService $econt)
    {
    }

    public function key(): string
    {
        return 'econt';
    }

    public function destinationTypes(): array
    {
        return [DestinationType::Office, DestinationType::Address];
    }

    public function rules(DestinationType $type): array
    {
        $place = [
            'place_id' => ['required', 'integer'],
            'city'     => ['required', 'string', 'max:255'],
        ];

        if ($type === DestinationType::Office) {
            return [
                ...$place,
                'point_code' => ['required', 'string', 'max:255'],
                'point_name' => ['required', 'string', 'max:255'],
            ];
        }

        return [
            ...$place,
            'postal_code'   => ['nullable', 'string', 'max:20'],
            'street'        => ['required', 'string', 'max:255'],
            'street_number' => ['required', 'string', 'max:255'],
            'note'          => ['nullable', 'string', 'max:255'],
        ];
    }

    public function destination(DestinationType $type, array $choice): Destination
    {
        return new Destination(
            method: $this->key(),
            type: $type,
            countryCode: $choice['country_code'],
            city: $choice['city'],
            postalCode: $choice['postal_code'] ?? null,
            street: $choice['street'] ?? null,
            streetNumber: $choice['street_number'] ?? null,
            note: $choice['note'] ?? null,
            pointCode: $choice['point_code'] ?? null,
            pointName: $choice['point_name'] ?? null,
            courierData: ['city_id' => (int) $choice['place_id']],
        );
    }

    /**
     * Econt prices the delivery itself, in the currency of its answer.
     */
    public function price(Destination $destination, Parcel $parcel): ?Money
    {
        $shipment = $destination->type === DestinationType::Office
            ? $this->econt->getOfficeShipment((string) $destination->pointCode)
            : $this->econt->getAddressShipment(
                receiverCityId: (int) $destination->courierData['city_id'],
                receiverStreet: (string) $destination->street,
                receiverStreetNum: (string) $destination->streetNumber,
            );

        return $shipment->getPrice();
    }

    public function places(string $search): array
    {
        $places = [];

        foreach ($this->econt->getEcontCities() as $city) {
            if (! self::matches($search, $city->name, $city->nameEn)) {
                continue;
            }

            $places[] = new Place((string) $city->id, (string) $city->name, $city->postCode);

            if (count($places) === self::SEARCH_LIMIT) {
                break;
            }
        }

        return $places;
    }

    public function points(string $placeId, DestinationType $type): array
    {
        if ($type !== DestinationType::Office) {
            return [];
        }

        $points = [];

        foreach ($this->econt->getEcontOffices(self::COUNTRY, new City(id: (int) $placeId)) as $office) {
            if ($office->code !== null) {
                $points[] = new Point($office->code, (string) $office->name);
            }
        }

        return $points;
    }

    public function streets(string $placeId, string $search): array
    {
        $streets = [];

        foreach ($this->econt->getEcontStreets(new City(id: (int) $placeId)) as $street) {
            if (! self::matches($search, $street->name, $street->nameEn)) {
                continue;
            }

            $streets[] = new Street((string) $street->name, $street->id === null ? null : (string) $street->id);

            if (count($streets) === self::SEARCH_LIMIT) {
                break;
            }
        }

        return $streets;
    }

    /**
     * A place or a street is found by its Bulgarian or its English name.
     */
    private static function matches(string $search, ?string ...$names): bool
    {
        $search = trim($search);

        if ($search === '') {
            return true;
        }

        foreach ($names as $name) {
            if ($name !== null && mb_stripos($name, $search) !== false) {
                return true;
            }
        }

        return false;
    }
}
