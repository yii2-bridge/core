<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/23/17
 * Time: 16:53
 */

namespace naffiq\bridge\gii\helpers;

/**
 * Class ColumnHelper
 *
 * Helps determine
 *
 * @package naffiq\bridge\gii\helpers
 */
class ColumnHelper
{
    /**
     * Checks if `$columnName` ends with `$endString`
     *
     * @param string $columnName
     * @param array|string $endString
     * @param bool $strict
     * @return bool
     */
    public static function endsWith($columnName, $endString, $strict = false)
    {
        if (is_array($endString)) {
            foreach ($endString as $string) {
                if (static::endsWith($columnName, $string)) {
                    return true;
                }
            }
            return false;
        } else {
            $lowerName = $strict ? $columnName : mb_strtolower($columnName);

            return strpos($lowerName, $endString) === strlen($lowerName) - strlen($endString);
        }
    }

    /**
     * Checks if `$columnName` begins with `$begin`
     *
     * @param string $columnName
     * @param array|string $beginString
     * @param bool $strict
     * @return bool
     */
    public static function beginsWith($columnName, $beginString, $strict = false)
    {
        if (is_array($beginString)) {
            foreach ($beginString as $string) {
                if (static::beginsWith($columnName, $string)) {
                    return true;
                }
            }
            return false;
        } else {
            $lowerName = $strict ? $columnName : mb_strtolower($columnName);

            return strpos($lowerName, $beginString) === 0;
        }
    }

    /**
     * Generates tabs indentation
     *
     * @param int $n
     * @return string
     */
    public static function pushTab($n = 1)
    {
        $tab = '    ';
        $result = '';
        for ($i = 0; $i < $n; $i++) {
            $result .= $tab;
        }
        return $result;
    }
}