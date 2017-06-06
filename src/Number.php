<?php

namespace Yeosz\Dtool;

class Number
{
    /**
     * 随机浮点数
     *
     * @param int $integer
     * @param int $decimal
     * @return float
     */
    public static function randomFloat($integer = 10, $decimal = 2)
    {
        $max = str_repeat('9', $integer);
        $i = mt_rand(0, intval($max));
        $max = intval(str_repeat('9', $decimal));
        $d = mt_rand(0, intval($max));
        $number = $i + ($d / pow(10, $decimal));
        return round($number, $decimal);
    }

    /**
     * tinyint
     *
     * @param $start
     * @param int $end
     * @return int
     */
    public static function randomTinyint($start = 0, $end = 127)
    {
        return mt_rand($start, $end);
    }

    /**
     * smallint
     *
     * @param $start
     * @param int $end
     * @return int
     */
    public static function randomSmallint($start = 0, $end = 32767)
    {
        return mt_rand($start, $end);
    }

    /**
     * mediumint
     *
     * @param $start
     * @param int $end
     * @return int
     */
    public static function randomMediumint($start = 0, $end = 8388607)
    {
        return mt_rand($start, $end);
    }

    /**
     * int
     *
     * @param $start
     * @param int $end
     * @return int
     */
    public static function randomInt($start = 0, $end = 2147483647)
    {
        return mt_rand($start, $end);
    }

    /**
     * bigint
     *
     * @param $start
     * @param int $end
     * @return int
     */
    public static function randomBigint($start = 0, $end = 2147483647)
    {
        return mt_rand($start, $end);
    }
}