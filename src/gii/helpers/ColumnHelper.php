<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/23/17
 * Time: 16:53
 */

namespace naffiq\bridge\gii\helpers;


use yii\db\ColumnSchema;

class ColumnHelper
{
    /**
     * Checks if `$column` ends with `$endString`
     *
     * @param ColumnSchema $column
     * @param $endString
     * @return bool
     */
    public static function endsWith(ColumnSchema $column, $endString)
    {
        $columnName = strtolower($column->name);
        if (is_array($endString)) {
            foreach ($endString as $string) {
                if (static::endsWith($column, $string)) {
                    return true;
                }
            }
            return false;
        } else {
            return strpos($columnName, $endString) === strlen($columnName) - strlen($endString);
        }
    }

    /**
     * Checks if `$column` begins with `$begin`
     *
     * @param ColumnSchema $column
     * @param $beginString
     * @return bool
     */
    public static function beginsWith(ColumnSchema $column, $beginString)
    {
        $columnName = strtolower($column->name);
        if (is_array($beginString)) {
            foreach ($beginString as $string) {
                if (static::beginsWith($column, $string)) {
                    return true;
                }
            }
            return false;
        } else {
            return strpos($columnName, $beginString) === 0;
        }
    }

    public static function pushTab($n = 1)
    {
        $tab = '    ';
        for ($result = ''; strlen($result) / strlen($tab) < $n; $result .= $tab);
        return $result;
    }
}