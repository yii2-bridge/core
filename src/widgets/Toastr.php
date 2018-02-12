<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 2/12/18
 * Time: 07:41
 */

namespace naffiq\bridge\widgets;


use yii\base\BaseObject;
use yii\web\View;

/**
 * Class Toastr
 *
 * Send toasts through session flash!
 *
 * @package naffiq\bridge\widgets
 */
class Toastr extends BaseObject
{
    const FLASH_KEY = 'bridge-toastr';

    /**
     * Adds info toast
     *
     * @param $text
     * @param null $title
     */
    public static function info($text, $title = null)
    {
        static::putFlash('info', $text, $title);
    }

    /**
     * Adds warning toast
     *
     * @param $text
     * @param null $title
     */
    public static function warning($text, $title = null)
    {
        static::putFlash('warning', $text, $title);
    }

    /**
     * Adds success toast
     *
     * @param $text
     * @param null $title
     */
    public static function success($text, $title = null)
    {
        static::putFlash('warning', $text, $title);
    }

    /**
     * Adds error toast
     *
     * @param $text
     * @param null $title
     */
    public static function error($text, $title = null)
    {
        static::putFlash('error', $text, $title);
    }

    /**
     * Register toasts within view JS code block
     *
     * @param View $view
     */
    public static function registerToasts(View $view)
    {
        $flashes = \Yii::$app->session->getFlash(static::FLASH_KEY, []);
        foreach ($flashes as $flash) {
            $title = !empty($flash['title']) ? ", '{$flash['title']}'" : '';
            $view->registerJs("toastr.{$flash['type']}('{$flash['text']}'{$title})", View::POS_END);
        }
    }

    protected static function putFlash($type, $text, $title = null)
    {
        $flashes = \Yii::$app->session->getFlash(static::FLASH_KEY, []);
        $flashes[] = [
            'type' => $type,
            'text' => $text,
            'title' => $title
        ];
        \Yii::$app->session->setFlash(static::FLASH_KEY, $flashes);
    }
}