<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/5/2017
 * Time: 1:11 AM
 */

namespace naffiq\bridge\controllers;

use yii2tech\admin\CrudController;

class BaseAdminController extends CrudController
{
    /**
     * @var string
     */
    public $layout = '@app/modules/admin/views/layouts/main';
}