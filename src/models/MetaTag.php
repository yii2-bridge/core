<?php

namespace Bridge\Core\Models;

use Bridge\Core\Models\Query\MetaTagQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "meta_tags".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property MetaModel $metaModel
 * @property MetaTagTranslation[] $metaTagTranslations
 * @property MetaTagTranslation $translation
 */
class MetaTag extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meta_tags';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('bridge', 'ID'),
            'created_at' => Yii::t('bridge', 'Created At'),
            'updated_at' => Yii::t('bridge', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetaModel()
    {
        return $this->hasOne(MetaModel::class, ['meta_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetaTagTranslations()
    {
        return $this->hasMany(MetaTagTranslation::class, ['meta_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetaPage()
    {
        return $this->hasOne(MetaPage::class, ['meta_tag_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return MetaTagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MetaTagQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            'translation' => [
                'class' => '\Bridge\Core\Behaviors\TranslationBehavior',
                'translationModelClass' => MetaTagTranslation::class,
                'translationModelRelationColumn' => 'meta_tag_id'
            ]
        ];
    }

    /**
     * Создание нового объекта класса MetaTag, со значениями по-умолчанию (если они указаны)
     *
     * @param array $defaultParams
     * @return MetaTag|false
     */
    public static function create($defaultParams = [])
    {
        $metaTag = new MetaTag();

        // Добавляем в POST запрос данные, которые пришли как по-умолчанию
        Yii::$app->request->setBodyParams(ArrayHelper::merge(self::getTranslationParams($metaTag, $defaultParams), Yii::$app->request->getBodyParams()));

        return $metaTag->save() ? $metaTag : false;
    }

    /**
     * Возвращаем массив со своими значениями по-умолчанию для перевода мета-тегов
     * Пример:
     *  'MetaTagTranslation' => [
     *      'en-US' => [
     *          'lang' => 'en-US',
     *          'title' => 'Title'
     *      ],
     *      'ru-RU' => [
     *          'lang' => 'ru-RU',
     *          'title' => 'Заголовок'
     *      ],
     *      'kk-KZ' => [
     *          'lang' => 'kk-KZ',
     *          'title' => 'Тақырып'
     *      ]
     *  ]
     *
     * @param MetaTag $metaTag
     * @param array $defaultParams
     * @return array
     */
    private static function getTranslationParams(MetaTag $metaTag, $defaultParams = [])
    {
        /**
         * Получаем базовое имя модели переводов мета-тегов (без namespace)
         * Пример: 'MetaTagTranslation'
         *
         * @var string $metaTagTranslationModelName
         */
        $metaTagTranslationModelName = StringHelper::basename($metaTag->getBehavior('translation')->translationModelClass);

        $params = [];

        foreach (Yii::$app->urlManager->languages as $label => $code) {
            $params[$metaTagTranslationModelName][$code] = [
                'lang' => $code,
                'title' => ArrayHelper::getValue($defaultParams, $code . '.title', Yii::$app->name),
                'description' => ArrayHelper::getValue($defaultParams, $code . '.description', Yii::$app->name)
            ];
        }

        return $params;
    }
}
