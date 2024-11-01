<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Max Morokko
 * Date: 8/5/15
 * Time: 2:01 PM
 * To change this template use File | Settings | File Templates.
 */

namespace SolidOpinion;

/**
 * Class SOUtils
 * @package SolidOpinion
 */
class SOUtils
{
    private static $_keylen = 32;
    private static $_ttl = 30;

    /**
     * Length of string
     * @param string $string string
     * @return int
     */
    public static function strlen($string)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($string, '8bit');
        } else {
            return strlen($string);
        }
    }

    /**
     * Key length
     * @return int
     */
    public static function keylen()
    {
        return self::$_keylen;
    }

    /**
     * Timestamp TTL
     * @return int
     */
    public static function ttl()
    {
        return self::$_ttl;
    }
}