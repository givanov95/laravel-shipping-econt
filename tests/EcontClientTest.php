<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Tests;

use Givanov95\Shipping\Econt\Components\City\City;
use Givanov95\Shipping\Econt\Components\Country\Country;
use Givanov95\Shipping\Econt\Econt;
use Givanov95\Shipping\Econt\Requests\GetCitiesRequest;
use Givanov95\Shipping\Econt\Requests\GetOfficesRequest;
use Givanov95\Shipping\Econt\Shipments\EcontOfficeShipment;
use Givanov95\TypedHttp\Exceptions\RequestException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

final class EcontClientTest extends TestCase
{
    public function test_cities_are_requested_with_basic_auth_and_a_json_body(): void
    {
        Http::fake(['econt.test/*' => Http::response(['cities' => [['id' => 1, 'name' => 'Varna']]])]);

        $response = (new Econt())->getCities(new GetCitiesRequest('BG'));

        $this->assertSame('Varna', $response->cities[0]->name);
        Http::assertSent(fn (Request $request) => $request->url() === 'https://econt.test/ee/services/Nomenclatures/NomenclaturesService.getCities.json'
            && $request->method() === 'POST'
            && $request->header('Authorization') === ['Basic '.base64_encode('user:pass')]
            && $request->header('Content-Type') === ['application/json']
            && $request->body() === '{"countryCode":"BG"}');
    }

    public function test_offices_payload_carries_the_city_id(): void
    {
        Http::fake(['econt.test/*' => Http::response(['offices' => []])]);
        $city = new City(id: 41, country: new Country(iso2Code: 'BG'));

        $offices = (new Econt())->getOffices(new GetOfficesRequest('BG', $city))->offices;

        $this->assertSame([], $offices);
        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/NomenclaturesService.getOffices.json')
            && $request->data() === ['countryCode' => 'BG', 'cityID' => 41]);
    }

    public function test_an_office_shipment_reads_the_price_from_the_label(): void
    {
        Http::fake(['econt.test/*' => Http::response(['label' => ['currency' => 'BGN', 'totalPrice' => 7.5]])]);

        $shipment = new EcontOfficeShipment(
            senderName: 'Shop',
            senderPhones: ['+359888000000'],
            senderCountryIso2Code: 'BG',
            senderCityId: 41,
            senderStreet: 'Main',
            senderStreetNum: '1',
            receiverName: 'John Doe',
            receiverPhones: ['+359888888888'],
            receiverCountryIso2Code: 'BG',
            receiverOfficeCode: '1042',
            packageWeight: 1.0,
            description: 'Parcel',
        );

        $this->assertSame('7.50', (string) $shipment->getPrice()->getAmount());
        Http::assertSent(function (Request $request) {
            $label = $request->data()['label'];

            return str_ends_with($request->url(), '/Shipments/LabelService.createLabel.json')
                && $request->data()['mode'] === 'calculate'
                && $label['receiverOfficeCode'] === '1042'
                && $label['receiverAddress'] === null;
        });
    }

    public function test_an_error_response_raises_a_request_exception_with_the_body(): void
    {
        Http::fake(['econt.test/*' => Http::response(['message' => 'bad'], 500)]);

        try {
            (new Econt())->getCities(new GetCitiesRequest('BG'));
            $this->fail('RequestException expected');
        } catch (RequestException $e) {
            $this->assertSame(500, $e->status());
            $this->assertSame('bad', $e->response()->json('message'));
        }
    }
}
