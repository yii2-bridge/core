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

class TranslationBehavior extends Behavior
{
    /** @var ActiveRecord */
    public $owner;

    /** @var string */
    public $translationModelClass;


    public $translationModelLangColumn = 'lang';


    public $translationModelRelationColumn;

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
     * @param null $languageCode
     *
     * @return ActiveRecord
     */
    public function getTranslation($languageCode = null)
    {
        return $this->getTranslationModel($languageCode ?: \Yii::$app->language);
    }

    public function saveTranslations()
    {
        /** @var ActiveRecord $translationModel */
        $translationModel = new $this->translationModelClass();

        $data = \Yii::$app->request->post($translationModel->formName());
        foreach ($data as $lang => $record) {
            $translation = $this->getTranslationModel($lang);
            $translation->setAttributes($record, [
                'post_id' => $this->owner->id,
                'lang' => $lang,
            ]);

            if (!$translation->save()) {
                Toastr::warning(\Yii::t('admin', "Перевод на {$lang} неполный, не сохранен"));
            }
        }
    }

    /**
     * @param $lang
     * @return mixed
     */
    protected function getTranslationModel($lang)
    {
        $translationClass = $this->translationModelClass;
        $translation = $translationClass::findOne([
            $this->translationModelRelationColumn => $this->owner->id,
            $this->translationModelLangColumn => $lang
        ]);

        if (!$translation) {
            $translation = new $translationClass([
                $this->translationModelRelationColumn => $this->owner->id,
                $this->translationModelLangColumn => $lang
            ]);
        }

        return $translation;
    }
}