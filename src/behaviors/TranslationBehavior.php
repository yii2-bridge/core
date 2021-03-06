<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 3/20/18
 * Time: 17:07
 */

namespace Bridge\Core\Behaviors;

use Bridge\Core\Widgets\Toastr;
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
     * @param null $cacheKey
     *
     * @return ActiveRecord
     */
    public function getTranslation($languageCode = null, $cacheKey = null)
    {
        return $this->getTranslationModel($languageCode ?: \Yii::$app->language, $cacheKey);
    }

    /**
     * Method that saves translation to model
     */
    public function saveTranslations()
    {
        /** @var ActiveRecord $translationModel */
        $translationModel = new $this->translationModelClass();

        $data = \Yii::$app->request->post($translationModel->formName());

        if (empty($data)) return;

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
     * @param $cacheKey
     * @return mixed
     */
    protected function getTranslationModel($lang, $cacheKey = null)
    {
        $cacheKey = $cacheKey !== null ? $cacheKey . '-' . $lang : false;

        $translationClass = $this->translationModelClass;

        if ($cacheKey) {
            $translation = \Yii::$app->cache->getOrSet($cacheKey, function () use ($translationClass, $lang) {
                return $translationClass::findOne([
                    $this->translationModelRelationColumn => $this->owner->getPrimaryKey(),
                    $this->translationModelLangColumn => $lang
                ]);
            });
        } else {
            $translation = $translationClass::findOne([
                $this->translationModelRelationColumn => $this->owner->getPrimaryKey(),
                $this->translationModelLangColumn => $lang
            ]);
        }

        if (!$translation) {
            $translation = new $translationClass([
                $this->translationModelRelationColumn => $this->owner->getPrimaryKey(),
                $this->translationModelLangColumn => $lang
            ]);
        }

        return $translation;
    }
}