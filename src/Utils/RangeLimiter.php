<?php

namespace App\Utils;

class RangeLimiter
{
    public static function clamp(int $value, int $min, int $max): int
    {
        return max($min, min($max, $value));
    }
}
