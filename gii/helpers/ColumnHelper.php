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

    public static function pushTab($n = 1)
    {
        $tab = '    ';
        for ($result = ''; strlen($result) / strlen($tab) < $n; $result .= $tab);
        return $result;
    }
}