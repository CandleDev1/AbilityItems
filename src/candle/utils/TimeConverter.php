<?php

namespace candle\utils;

class TimeConverter
{
    public static function ticksToSeconds(int $ticks): int
    {
        return intdiv($ticks, 20);
    }

}