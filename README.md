# Laravel Shipping — Econt

Econt as a delivery method of a Laravel shop: the client of the Econt services API (on [`givanov95/typed-http`](https://github.com/givanov95/typed-http)) and the adapter that puts it behind the delivery contract of [`givanov95/laravel-shipping`](https://github.com/givanov95/laravel-shipping). The shop needs no code for Econt: the package registers the method, the shop offers it.

```bash
composer require givanov95/laravel-shipping-econt
```

> **0.x, not yet checked against the real API.** The requests and the answers are covered with `Http::fake()` only: the tests prove what is sent and how the answer we expect is read. Run the checks under [Before going live](#before-going-live) with your own account first.

## What the shop gets

The service provider is auto-discovered. It registers the method under the key `econt` with the label `Econt`:

| | |
| --- | --- |
| Delivers to | an office (`office`), or an address (`address`) in a city Econt knows by id |
| Checkout lists | `PlaceDirectory`: the cities found by their Bulgarian or English name, the offices of a city, its streets |
| Price | asked from Econt per order (`LabelService.createLabel` in mode `calculate`), in the currency of its answer |
| Kept on the order | the `Destination`, with the id of the city in `courierData` (`city_id`) |

Registered is not offered: which methods a customer can pick, and in what order, is the decision of the shop. It finds this one in the registry by its key, the key an order keeps: `app(Givanov95\Shipping\DeliveryMethods::class)->get('econt')`.

Econt prices in the currency of the account. The contract has no exchange rate, so a shop refuses the price for a cart in another currency.

## Configuration

```dotenv
ECONT_BASE_URL="http://demo.econt.com/ee/services"
ECONT_USERNAME=
ECONT_PASSWORD=
ECONT_SENDER_NAME=
ECONT_SENDER_PHONE=
ECONT_SENDER_CITY=
ECONT_SENDER_STREET=
ECONT_SENDER_STREET_NUM=
ECONT_PACKAGE_DESCRIPTION="Parcel"
```

| Key | What it is |
| --- | --- |
| `ECONT_BASE_URL` | The services API: `http://demo.econt.com/ee/services` (demo, with the demo account Econt publishes in its documentation) or `https://ee.econt.com/services` (live) |
| `ECONT_USERNAME`, `ECONT_PASSWORD` | The account, sent as Basic auth |
| `ECONT_SENDER_*` | Where the parcels leave from; `ECONT_SENDER_CITY` is the id Econt gives the city |
| `ECONT_PACKAGE_DESCRIPTION` | The contents written on the label |

The values are read through `config('econt.*')`. To change the file itself:

```bash
php artisan vendor:publish --tag=econt-config
```

The cities are cached until asked for again (`php artisan econt:re-cache-cities`); a failed request is never cached, and the cached list is replaced only by a real answer.

## Before going live

Nothing here has been sent to Econt yet. With the demo account, from `php artisan tinker`:

- `getCities` — `(new Givanov95\Shipping\Econt\Econt())->getCities(new Givanov95\Shipping\Econt\Requests\GetCitiesRequest('BG'))->cities` is a list with `id`, `name`, `nameEn`, `postCode`.
- `getOffices` for a city and `getStreets` — the answers have `offices` (with `code`, `name`) and `streets` (with `id`, `name`, `nameEn`).
- The price — `createLabel` in mode `calculate` answers with `label.totalPrice` and `label.currency`, for an office and for an address.
- The weight is fixed at 1 kg and the recipient of the calculation is a placeholder: check that the price does not depend on them for your parcels, or open an issue.

Only requests without side effects: `calculate` creates no shipment. Record what the API really answers as fixtures without personal data and tokens, and fix the code and the tests where they differ.

## Testing

```bash
composer test
```

## License

MIT
