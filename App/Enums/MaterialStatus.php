<?php

namespace App\Enums;


enum MaterialStatus: string
{
    case New = "new";
    case Used = "used";
    case BROKEN = "broken-down";

    /**
     * Change a string value to an Enum 
     */
    public static function getEnumFromValue(string $value): ?MaterialStatus
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        return null;
    }
}
