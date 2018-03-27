<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 3/20/18
 * Time: 17:07
 */

namespace naffiq\bridge\behaviors;

use naffiq\bridge\widgets\Toastr;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class TranslationBehavior extends Behavior
{
    /** @var ActiveRecord */
    public $owner;

    /** @var string */
    public $translationModelClass;

    /**
     * @var string
     */
    public $translationModelLangColumn = 'lang';

    /**
     * @var string
     */
    public $translationModelRelationColumn = 'parent_id';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveTranslations',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveTranslations'
        ];
    }

    /**
     * Returns translation model. If no `languageCode` is provided, then application language is used.
     *
     * @param null $languageCode
     *
     * @return ActiveRecord
     */
    public function getTranslation($languageCode = null)
    {
        return $this->getTranslationModel($languageCode ?: \Yii::$app->language);
    }

    /**
     * Method that saves translation to model
     */
    public function saveTranslations()
    {
        /** @var ActiveRecord $translationModel */
        $translationModel = new $this->translationModelClass();

        $data = \Yii::$app->request->post($translationModel->formName());
        foreach ($data as $lang => $record) {
            $translation = $this->getTranslationModel($lang);
            $translation->setAttributes(ArrayHelper::merge($record, [
                $this->translationModelRelationColumn => $this->owner->getPrimaryKey(),
                $this->translationModelLangColumn => $lang,
            ]));

            if (!$translation->save()) {
                Toastr::warning(\Yii::t('admin', "Перевод на {$lang} неполный, не сохранен"));
            }
        }
    }

    /**
     * Returns required translation model based on `lang` param provided.
     *
     * @param $lang string language code
     * @return mixed
     */
    protected function getTranslationModel($lang)
    {
        $translationClass = $this->translationModelClass;
        $translation = $translationClass::findOne([
            $this->translationModelRelationColumn => $this->owner->getPrimaryKey(),
            $this->translationModelLangColumn => $lang
        ]);

        if (!$translation) {
            $translation = new $translationClass([
                $this->translationModelRelationColumn => $this->owner->getPrimaryKey(),
                $this->translationModelLangColumn => $lang
            ]);
        }

        return $translation;
    }
}