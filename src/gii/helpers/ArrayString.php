<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/22/17
 * Time: 18:32
 */

namespace Bridge\Core\Gii\Helpers;

/**
 * Class ArrayToString
 * @package Bridge\Core\Gii\Helpers
 */
class ArrayString
{
    protected $data;
    protected $noKeys;

    public function __construct($data, $noKeys = false)
    {
        $this->data = $data;
        $this->noKeys = $noKeys;
    }

    public function __toString()
    {
        $output = [];
        foreach ($this->data as $key => $value) {
            $outputValue = is_string($value) ? "'$value'" : $value;
            $output[] = $this->noKeys ? "$outputValue" : "'$key' => $outputValue";
        }
        return '[' .implode(', ', $output). ']';
    }

}