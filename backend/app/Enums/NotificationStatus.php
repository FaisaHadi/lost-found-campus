<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case Unread = 'unread';
    case Read = 'read';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
