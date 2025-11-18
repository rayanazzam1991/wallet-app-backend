<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum TransactionType: string
{
    use EnumValues;
    case ALL = 'all';
    case SENT = 'sent';
    case RECEIVED = ' received';
}
