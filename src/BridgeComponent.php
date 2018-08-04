<?php
/**
 * Created by PhpStorm.
 * User: abdug
 * Date: 18.04.2018
 * Time: 1:17
 */

namespace Bridge\Core;


use yii\base\Component;

/**
 * Class BridgeComponent
 *
 * Service component for storing state and settings
 *
 * @package Bridge\Core
 */
class BridgeComponent extends Component
{
    /** @var bool */
    protected $isAdmin = false;

    /**
     * Sets app state to admin mode
     */
    public function setIsAdmin()
    {
        $this->isAdmin = true;
    }

    /**
     * Returns admin mode value
     *
     * @return bool
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }
}