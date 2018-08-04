<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 3/20/18
 * Time: 16:57
 */

namespace Bridge\Core\Widgets;


use naffiq\bridge\BridgeModule;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class TranslationFormWidget extends Widget
{
    public $languages;
    public $form;
    public $model;
    public $viewName;

    public function init()
    {
        if (empty($this->languages)) {
            $this->languages = [];
            /** @var BridgeModule $adminModule */
            $adminModule = \Yii::$app->getModule('admin');
            foreach (\Yii::$app->urlManager->languages as $label => $code) {
                $this->languages[$code] = ArrayHelper::getValue($adminModule->languages, $code, $label);
            }
        }

    }

    public function run()
    {
        return $this->render('translation-form', [
            'languages' => $this->languages,
            'form' => $this->form,
            'model' => $this->model,
            'viewName' => $this->viewName
        ]);
    }
}