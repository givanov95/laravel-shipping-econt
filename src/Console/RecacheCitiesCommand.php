<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Console;

use Givanov95\Shipping\Econt\EcontService;
use Illuminate\Console\Command;

/**
 * The cities are cached until they are asked for again: Econt adds one rarely.
 */
final class RecacheCitiesCommand extends Command
{
    protected $signature = 'econt:re-cache-cities';

    protected $description = 'Read the cities of Econt again and replace the cached list';

    public function handle(EcontService $econt): int
    {
        $cities = $econt->recacheCities();

        if ($cities === []) {
            $this->error('Econt gave no cities; the cached list is kept.');

            return self::FAILURE;
        }

        $this->info(sprintf('%d cities cached.', count($cities)));

        return self::SUCCESS;
    }
}
