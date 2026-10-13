<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt;

use Givanov95\TypedHttp\Auth\BasicAuth;
use Givanov95\TypedHttp\Connector;
use Givanov95\TypedHttp\Contracts\Authenticator;

/**
 * The Econt services API: Basic auth, credentials and base URL from config/econt.php.
 */
final class EcontConnector extends Connector
{
    public function baseUrl(): string
    {
        return (string) config('econt.base_url');
    }

    public function authenticator(): Authenticator
    {
        return new BasicAuth((string) config('econt.username'), (string) config('econt.password'));
    }
}
