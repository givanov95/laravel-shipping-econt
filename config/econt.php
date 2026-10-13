<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Econt
    |--------------------------------------------------------------------------
    |
    | The services API of Econt and the account it is called with. The demo
    | environment is http://demo.econt.com/ee/services, the live one
    | https://ee.econt.com/services.
    |
    */
    'base_url' => env('ECONT_BASE_URL'),
    'username' => env('ECONT_USERNAME'),
    'password' => env('ECONT_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Sender
    |--------------------------------------------------------------------------
    |
    | Where the parcels leave from: Econt prices a delivery from this address.
    | `city_id` is the id Econt gives the city (NomenclaturesService.getCities).
    |
    */
    'sender' => [
        'name'          => env('ECONT_SENDER_NAME'),
        'phone'         => env('ECONT_SENDER_PHONE'),
        'city_id'       => env('ECONT_SENDER_CITY'),
        'street'        => env('ECONT_SENDER_STREET'),
        'street_number' => env('ECONT_SENDER_STREET_NUM'),
    ],

    'package_description' => env('ECONT_PACKAGE_DESCRIPTION', 'Parcel'),
];
