<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/11/2017
 * Time: 5:01 PM
 */

namespace naffiq\bridge\widgets;

use yii\base\Widget;
use yii\helpers\Url;

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
        if (!empty($item['items'])) {
            foreach ($item['items'] as $subItem) {
                if (self::isActive($subItem)) {
                    return true;
                }
            }
        }

        if (empty($item['active']) && !empty($item['url'])) {

            if (Url::current() == Url::to($item['url'])) {
                return true;
            }

            return false;
        }
        $active = $item['active'];

        if (is_array($active)) {
            if (!isset($active['module']) && !isset($active['controller']) && !isset($active['action'])) {
                return false;
            }

            if (isset($active['module']) && $active['module'] != \Yii::$app->controller->module->id) {
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

    /**
     * Checks if menu item should be visible
     *
     * @param array $item
     * @return bool
     */
    public static function isVisible($item) {
        if (empty($item['isVisible'])) {
            return true;
        }

        $isVisible = $item['isVisible'];

        if (is_callable($isVisible)) {
            return $isVisible($item);
        }

        if (is_array($isVisible)) {
            foreach ($isVisible as $role) {
                if (\Yii::$app->user->can($role)) {
                    return true;
                }

                return false;
            }
        }

        if (is_bool($isVisible)) {
            return $isVisible;
        }

        throw new \InvalidArgumentException('Invalid key type provided for `isVisible` in menu');
    }
}