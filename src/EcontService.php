<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt;

use Exception;
use Givanov95\Shipping\Econt\Components\City\City;
use Givanov95\Shipping\Econt\Requests\GetCitiesRequest;
use Givanov95\Shipping\Econt\Requests\GetOfficesRequest;
use Givanov95\Shipping\Econt\Requests\GetStreetsRequest;
use Givanov95\Shipping\Econt\Shipments\EcontAddressShipment;
use Givanov95\Shipping\Econt\Shipments\EcontOfficeShipment;
use Illuminate\Support\Facades\Cache;
use Throwable;

class EcontService
{
    private const CITIES_CACHE_KEY = 'econt-cities';

    public function getAddressShipment(int $receiverCityId, string $receiverStreet, string $receiverStreetNum)
    {
        return
            new EcontAddressShipment(
                senderName: config('econt.sender.name'),
                senderPhones: [config('econt.sender.phone')],
                senderCountryIso2Code: 'BG',
                senderCityId: (int) config('econt.sender.city_id'),
                senderStreet: config('econt.sender.street'),
                senderStreetNum: config('econt.sender.street_number'),
                receiverName: 'John Doe',
                receiverPhones: ['+359888888888'],
                receiverCountryIso2Code: 'BG',
                receiverCityId: $receiverCityId,
                receiverStreet: $receiverStreet,
                receiverStreetNum: $receiverStreetNum,
                packageWeight: 1,
                description: (string) config('econt.package_description'),
            );
    }

    public function getOfficeShipment(string $receiverOfficeCode)
    {
        return
            new EcontOfficeShipment(
                senderName: config('econt.sender.name'),
                senderPhones: [config('econt.sender.phone')],
                senderCountryIso2Code: 'BG',
                senderCityId: (int) config('econt.sender.city_id'),
                senderStreet: config('econt.sender.street'),
                senderStreetNum: config('econt.sender.street_number'),
                receiverName: 'John Doe',
                receiverPhones: ['+359888888888'],
                receiverCountryIso2Code: 'BG',
                receiverOfficeCode: $receiverOfficeCode,
                packageWeight: 1,
                description: (string) config('econt.package_description'),
            );
    }

    /**
     * The checkout form searches this list, so a failing Econt gives an empty one.
     * Only a real answer is cached: an empty one would otherwise stay forever.
     */
    public function getEcontCities(): array
    {
        $cached = Cache::get(self::CITIES_CACHE_KEY);

        if (! empty($cached)) {
            return $cached;
        }

        return $this->recacheCities();
    }

    /**
     * Asks Econt for the cities again. The cached list is replaced only by a real answer.
     */
    public function recacheCities(): array
    {
        try {
            $cities = (new Econt())->getCities(new GetCitiesRequest('BG'))->cities ?? [];
        } catch (Throwable $e) {
            report($e);

            return [];
        }

        if (! empty($cities)) {
            Cache::forever(self::CITIES_CACHE_KEY, $cities);
        }

        return $cities;
    }

    public function getEcontOffices(string $countryCode, City $city): array
    {
        if (! $city->id) {
            return [];
        }

        $econt = new Econt();
        $getOfficesRequest = new GetOfficesRequest($countryCode, $city);

        try {
            return $econt->getOffices($getOfficesRequest)->offices;
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    /**
     * Get Econt quarters for a given city.
     *
     * @param City $city
     */
    public function getEcontStreets(City $city): array
    {
        if (! $city) {
            return [];
        }

        $econt = new Econt();
        $getStreetsRequest = new GetStreetsRequest($city);

        try {
            return $econt->getStreets($getStreetsRequest)->streets;
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }
}
