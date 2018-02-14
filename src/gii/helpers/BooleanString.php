<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 2/14/18
 * Time: 23:59
 */

namespace naffiq\bridge\gii\helpers;


class BooleanString
{
    protected $value;

    /**
     * BooleanString constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = (bool) $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value ? 'true' : 'false';
    }
}