<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Enums;

enum LabelMode: string
{
    case Calculate = 'calculate';
    case Validate = 'validate';
    case Create = 'create';
    case Calculate_with_block = 'calculate_with_block';
}
