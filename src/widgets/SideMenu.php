<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/11/2017
 * Time: 5:01 PM
 */

namespace Bridge\Core\Widgets;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class SideMenu
 *
 * Renders side menu in admin panel
 *
 * @package Bridge\Core\Widgets
 */
class SideMenu extends Widget
{
    /**
     * @var array
     */
    public $items;

    /**
     * @var integer
     */
    public $isMenuWide;

    public function run()
    {
        return $this->render('side-menu', ['items' => $this->items, 'isMenuWide' => $this->isMenuWide]);
    }

    /**
     * Checking if item should be highlighted
     *
     * @param $item
     * @return bool
     */
    public static function isActive($item)
    {
        foreach (ArrayHelper::getValue($item, 'items', []) as $subItem) {
            if (self::isActive($subItem)) {
                return true;
            }
        }
        if (empty($item['active'])) {
            $url = explode('?', Url::current());
            return !empty($item['url']) && $url[0] === Url::to($item['url']);
        }

        $active = $item['active'];

        if (is_array($active)) {
            return static::isArrayItemActive($active);
        } elseif (is_callable($active)) {
            return $active($item);
        }

        return false;
    }

    /**
     * Returns true if menu item should be active for array configured items (`active` key)
     *
     * @param array $active
     * @return bool
     */
    protected static function isArrayItemActive($active)
    {
        if (!isset($active['module']) && !isset($active['controller']) && !isset($active['action'])) {
            return false;
        }
        $controller = \Yii::$app->controller;


        if ((!empty($active['module']) && $active['module'] != $controller->module->id)
            || (!empty($active['controller']) && $active['controller'] != $controller->id)
            || (!empty($active['action']) && $active['action'] != $controller->action->id)) {

            return false;
        }

        return true;
    }

    /**
     * Checks if menu item should be visible
     *
     * @param array $item
     * @return bool
     * @throws \InvalidArgumentException
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
            return static::checkMenuItemRoles($isVisible);
        }

        if (is_bool($isVisible)) {
            return $isVisible;
        }

        throw new \InvalidArgumentException('Invalid key type provided for `isVisible` in menu');
    }

    /**
     * Checks roles in `isVisible` menu item key if it's an array.
     *
     * @param array $isVisible
     * @return bool item visibility for role
     */
    public static function checkMenuItemRoles($isVisible) {
        foreach ($isVisible as $role) {
            if (\Yii::$app->user->can($role)) {
                return true;
            }
        }
        return false;
    }
}