<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum TransactionStatus:string
{
    use EnumValues;
    case PENDING = 'pending';
    case SUCCESS = 'success';
}
