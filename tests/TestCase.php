<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Tests;

use Givanov95\Shipping\Econt\EcontServiceProvider;
use Givanov95\Shipping\ShippingServiceProvider;
use Givanov95\TypedHttp\Laravel\TypedHttpServiceProvider;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as Testbench;

/**
 * An application with nothing of a shop in it: the delivery contract, the HTTP bridge and
 * this package. Every request to Econt is faked.
 */
abstract class TestCase extends Testbench
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        config([
            'econt.base_url' => 'https://econt.test/ee/services',
            'econt.username' => 'user',
            'econt.password' => 'pass',
            'econt.sender'   => ['name' => 'Shop Ltd', 'phone' => '+359111', 'city_id' => '41', 'street' => 'Main', 'street_number' => '7'],
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [TypedHttpServiceProvider::class, ShippingServiceProvider::class, EcontServiceProvider::class];
    }
}
