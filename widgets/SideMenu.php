<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/11/2017
 * Time: 5:01 PM
 */

namespace naffiq\bridge\widgets;

use yii\base\Widget;

class SideMenu extends Widget
{
    /**
     * @var array
     */
    public $items;

    public function run()
    {
        return $this->render('side-menu', ['items' => $this->items]);
    }

    /**
     * Checking if item should be highlighted
     *
     * @param $item
     * @return bool
     */
    public static function isActive($item)
    {
        if (empty($item['active'])) {
            return false;
        }
        $active = $item['active'];

        if (is_array($active)) {
            if (!isset($active['module']) && !isset($active['controller']) && !isset($active['action'])) {
                return false;
            }

            if (isset($active['module']) && $active['module'] != \Yii::$app->module->id) {
                return false;
            }

            if (isset($active['controller']) && $active['controller'] != \Yii::$app->controller->id) {
                return false;
            }

            if (isset($active['action']) && $active['action'] != \Yii::$app->controller->action->id) {
                return false;
            }

            return true;
        } elseif (is_callable($active)) {
            return $active($item);
        }

        return false;
    }
}