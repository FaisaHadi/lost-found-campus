<?php

namespace App\Enums;

enum ReportType: string
{
    case Lost = 'lost';
    case Found = 'found';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
