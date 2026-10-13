<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Tests;

use Givanov95\Shipping\Contracts\PlaceDirectory;
use Givanov95\Shipping\DeliveryMethods;
use Givanov95\Shipping\Destination;
use Givanov95\Shipping\DestinationType;
use Givanov95\Shipping\Econt\EcontMethod;
use Givanov95\Shipping\Econt\EcontService;
use Givanov95\Shipping\Econt\Tests\Fixtures\Basket;
use Givanov95\Shipping\Place;
use Givanov95\Shipping\Point;
use Givanov95\Shipping\Street;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

/**
 * Econt behind the delivery contract, the way a shop asks it: what the customer chooses,
 * the destination kept on the order, the price and the lists of the checkout form.
 */
final class EcontMethodTest extends TestCase
{
    /**
     * @param  array<string, mixed>  $label
     */
    private function fakeEcont(array $label = ['currency' => 'BGN', 'totalPrice' => 7.5]): void
    {
        Http::fake([
            'econt.test/*getCities.json' => Http::response(['cities' => [
                ['id' => 41, 'name' => 'София', 'nameEn' => 'Sofia', 'postCode' => '1000'],
                ['id' => 68, 'name' => 'Варна', 'nameEn' => 'Varna', 'postCode' => '9000'],
                ['id' => 73, 'name' => 'Софрониево', 'nameEn' => 'Sofronievo', 'postCode' => '3330'],
            ]]),
            'econt.test/*getOffices.json' => Http::response(['offices' => [
                ['id' => 1, 'code' => '1042', 'name' => 'София Център'],
                ['id' => 2, 'code' => '1127', 'name' => 'София Младост'],
            ]]),
            'econt.test/*getStreets.json' => Http::response(['streets' => [
                ['id' => 5, 'name' => 'бул. Витоша', 'nameEn' => 'Vitosha Blvd'],
                ['id' => 6, 'name' => 'ул. Раковски', 'nameEn' => 'Rakovski Str'],
            ]]),
            'econt.test/*createLabel.json' => Http::response(['label' => $label]),
        ]);
    }

    private function method(): EcontMethod
    {
        return $this->app->make(DeliveryMethods::class)->get('econt');
    }

    /**
     * @return array<string, mixed>
     */
    private function toOffice(): array
    {
        return [
            'country_code' => 'BG',
            'place_id'     => '41',
            'city'         => 'София',
            'point_code'   => '1042',
            'point_name'   => 'София Център',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function toAddress(): array
    {
        return [
            'country_code'  => 'BG',
            'place_id'      => '41',
            'city'          => 'София',
            'postal_code'   => '1000',
            'street'        => 'бул. Витоша',
            'street_number' => '15',
            'note'          => 'Floor 2',
        ];
    }

    /**
     * @param  array<string, mixed>  $choice
     * @return list<string> the fields the rules of the method refuse
     */
    private function refused(DestinationType $type, array $choice): array
    {
        return Validator::make($choice, $this->method()->rules($type))->errors()->keys();
    }

    public function test_the_package_registers_the_method_under_its_key(): void
    {
        $methods = $this->app->make(DeliveryMethods::class);

        $this->assertTrue($methods->has('econt'));
        $this->assertSame(['econt' => 'Econt'], $methods->labels());
        $this->assertInstanceOf(EcontMethod::class, $methods->get('econt'));
        $this->assertInstanceOf(PlaceDirectory::class, $methods->get('econt'));
        $this->assertSame([DestinationType::Office, DestinationType::Address], $methods->get('econt')->destinationTypes());
    }

    public function test_the_configuration_comes_with_the_package(): void
    {
        $this->assertSame('Parcel', config('econt.package_description'));
        $this->assertSame(['name', 'phone', 'city_id', 'street', 'street_number'], array_keys(config('econt.sender')));
    }

    public function test_an_office_needs_the_city_and_the_office(): void
    {
        $this->assertSame([], $this->refused(DestinationType::Office, $this->toOffice()));
        $this->assertSame(
            ['place_id', 'point_code'],
            $this->refused(DestinationType::Office, [...$this->toOffice(), 'place_id' => null, 'point_code' => null]),
        );
    }

    public function test_an_address_needs_the_city_the_street_and_the_number(): void
    {
        $this->assertSame([], $this->refused(DestinationType::Address, $this->toAddress()));
        $this->assertSame(
            ['street', 'street_number'],
            $this->refused(DestinationType::Address, [...$this->toAddress(), 'street' => null, 'street_number' => null]),
        );
        $this->assertSame(['place_id'], $this->refused(DestinationType::Address, [...$this->toAddress(), 'place_id' => 'Sofia']));
    }

    public function test_the_destination_of_an_office_keeps_the_city_econt_knows(): void
    {
        $destination = $this->method()->destination(DestinationType::Office, $this->toOffice());

        $this->assertSame(
            [
                'method'        => 'econt',
                'type'          => 'office',
                'country_code'  => 'BG',
                'city'          => 'София',
                'postal_code'   => null,
                'street'        => null,
                'street_number' => null,
                'note'          => null,
                'point_code'    => '1042',
                'point_name'    => 'София Център',
                'courier_data'  => ['city_id' => 41],
            ],
            $destination->toArray(),
        );
    }

    public function test_the_destination_of_an_address_keeps_the_street_and_the_note(): void
    {
        $destination = $this->method()->destination(DestinationType::Address, $this->toAddress());

        $this->assertSame(DestinationType::Address, $destination->type);
        $this->assertSame(['София', '1000', 'бул. Витоша', '15', 'Floor 2', null], [
            $destination->city, $destination->postalCode, $destination->street, $destination->streetNumber, $destination->note, $destination->pointCode,
        ]);
        $this->assertSame(['city_id' => 41], $destination->courierData);
    }

    public function test_an_office_is_priced_by_econt_from_the_address_of_the_sender(): void
    {
        $this->fakeEcont();

        $price = $this->method()->price($this->method()->destination(DestinationType::Office, $this->toOffice()), new Basket('BGN'));

        $this->assertSame(['7.50', 'BGN'], [(string) $price->getAmount(), $price->getCurrency()->getCurrencyCode()]);
        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/LabelService.createLabel.json')
            && $request->data()['mode'] === 'calculate'
            && $request->data()['label']['receiverOfficeCode'] === '1042'
            && $request->data()['label']['senderClient']['name'] === 'Shop Ltd'
            && $request->data()['label']['senderAddress']['city']['id'] === 41);
    }

    public function test_an_address_is_priced_for_the_city_econt_knows(): void
    {
        $this->fakeEcont(['currency' => 'BGN', 'totalPrice' => 9.2]);

        $price = $this->method()->price($this->method()->destination(DestinationType::Address, $this->toAddress()), new Basket('BGN'));

        $this->assertSame('9.20', (string) $price->getAmount());
        Http::assertSent(function (Request $request) {
            $address = $request->data()['label']['receiverAddress'] ?? null;

            return $address !== null
                && $address['city']['id'] === 41
                && $address['street']['name'] === 'бул. Витоша'
                && $address['number'] === '15';
        });
    }

    public function test_there_is_no_price_when_econt_gives_no_label(): void
    {
        Http::fake(['econt.test/*' => Http::response(['label' => null])]);

        $this->assertNull($this->method()->price($this->method()->destination(DestinationType::Office, $this->toOffice()), new Basket('BGN')));
    }

    /**
     * Nothing is converted: the shop refuses a price in another currency than the one of the parcel.
     */
    public function test_the_price_is_in_the_currency_econt_answers_in(): void
    {
        $this->fakeEcont(['currency' => 'EUR', 'totalPrice' => 3.8]);

        $price = $this->method()->price($this->method()->destination(DestinationType::Office, $this->toOffice()), new Basket('BGN'));

        $this->assertSame(['3.80', 'EUR'], [(string) $price->getAmount(), $price->getCurrency()->getCurrencyCode()]);
    }

    public function test_places_are_found_by_their_name_in_either_language(): void
    {
        $this->fakeEcont();

        $this->assertEquals(
            [new Place('41', 'София', '1000'), new Place('73', 'Софрониево', '3330')],
            $this->method()->places('соф'),
        );
        $this->assertEquals([new Place('68', 'Варна', '9000')], $this->method()->places('varna'));
        $this->assertCount(3, $this->method()->places(''));
    }

    public function test_the_offices_and_the_streets_of_a_city_are_listed(): void
    {
        $this->fakeEcont();

        $this->assertEquals(
            [new Point('1042', 'София Център'), new Point('1127', 'София Младост')],
            $this->method()->points('41', DestinationType::Office),
        );
        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/NomenclaturesService.getOffices.json')
            && $request->data()['cityID'] === 41);

        $this->assertEquals([new Street('бул. Витоша', '5')], $this->method()->streets('41', 'vito'));

        // Econt has no lockers behind the contract.
        $this->assertSame([], $this->method()->points('41', DestinationType::Locker));
    }

    public function test_a_failing_econt_gives_empty_lists_and_is_not_cached(): void
    {
        Http::fake(['econt.test/*' => Http::response('Service Unavailable', 503)]);

        $this->assertSame([], $this->method()->places('Sof'));
        $this->assertSame([], $this->method()->points('41', DestinationType::Office));
        $this->assertSame([], $this->method()->streets('41', ''));
        $this->assertFalse(Cache::has('econt-cities'), 'a failed request must not be cached');
    }

    public function test_the_cities_are_cached_only_once_econt_answers(): void
    {
        Http::fakeSequence('econt.test/*')
            ->push('Service Unavailable', 503)
            ->push(['cities' => [['id' => 1, 'name' => 'Varna']]]);
        $service = new EcontService();

        $this->assertSame([], $service->getEcontCities());
        $this->assertSame('Varna', $service->getEcontCities()[0]->name);
        $this->assertSame('Varna', $service->getEcontCities()[0]->name);

        Http::assertSentCount(2);
    }

    public function test_the_command_replaces_the_cached_cities_only_with_a_real_answer(): void
    {
        Http::fakeSequence('econt.test/*')
            ->push(['cities' => [['id' => 1, 'name' => 'Varna']]])
            ->push('Service Unavailable', 503)
            ->push(['cities' => [['id' => 1, 'name' => 'Varna'], ['id' => 2, 'name' => 'Burgas']]]);

        $this->artisan('econt:re-cache-cities')->expectsOutput('1 cities cached.')->assertSuccessful();
        $this->artisan('econt:re-cache-cities')->assertFailed();
        $this->assertCount(1, Cache::get('econt-cities'));

        $this->artisan('econt:re-cache-cities')->expectsOutput('2 cities cached.')->assertSuccessful();
        $this->assertCount(2, (new EcontService())->getEcontCities());
    }
}
