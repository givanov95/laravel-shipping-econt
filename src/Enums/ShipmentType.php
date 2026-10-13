<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Enums;

enum ShipmentType: string
{
    case Document = 'document';
    case Pack = 'pack';
    case Post_pack = 'post_pack';
    case Pallet = 'pallet';
    case Cargo = 'cargo';
    case Document_pallet = 'documentpallet';
    case Big_letter = 'big_letter';
    case Small_letter = 'small_letter';
    case Money_transfer = 'money_transfer';
    case Pp = 'pp'; // Post transfer
}
