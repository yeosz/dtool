<?php

namespace Yeosz\Dtool;

class Datetime
{

    /**
     * 年
     *
     * @param int $start
     * @param int $end
     * @return string
     */
    public static function year($start = 1900, $end = 2100)
    {
        return strval(mt_rand(intval($$start), intval($end)));
    }

    /**
     * 时间
     *
     * @return string
     */
    public static function time()
    {
        return strval(mt_rand(0, 23) . ':' . mt_rand(0, 59) . ':' . mt_rand(0, 59));
    }

    /**
     * 日期
     *
     * @param int $type
     * @param string $format
     * @return bool|string
     */
    public static function date($type = -1, $format = 'Y-m-d')
    {
        return date($format, self::getTimestamp($type));
    }

    /**
     * 获取时间戳
     *
     * @param int $type 负数过去时,0当前,正数将来时
     * @return integer
     */
    public static function getTimestamp($type = -1)
    {
        $time = time();

        if ($type < 0) {
            $time -= mt_rand(1800, 86400 * 365);
        } elseif ($type > 0) {
            $time += mt_rand(1800, 86400 * 365);
        }
        return $time;
    }

    /**
     * 获取日期时间
     *
     * @param int $type 负数过去时,0当前,正数将来时
     * @param string $format 格式
     * @return string
     */
    public static function datetime($type = -1, $format = 'Y-m-d H:i:s')
    {
        return date($format, self::getTimestamp($type));
    }
}