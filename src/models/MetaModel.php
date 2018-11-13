<?php

namespace Bridge\Core\Models;

use Bridge\Core\Behaviors\MetaTagBehavior;
use Bridge\Core\Models\Query\MetaModelQuery;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "meta_models".
 *
 * @property integer $id
 * @property string $model
 * @property integer $model_id
 * @property integer $meta_tag_id
 *
 * @property MetaTag $metaTag
 */
class MetaModel extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meta_models';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'model_id', 'meta_tag_id'], 'required'],
            [['model_id', 'meta_tag_id'], 'integer'],
            [['model'], 'string', 'max' => 255],
            [['meta_tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => MetaTag::class, 'targetAttribute' => ['meta_tag_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('bridge', 'ID'),
            'model' => Yii::t('bridge', 'Model class name'),
            'model_id' => Yii::t('bridge', 'Model item ID'),
            'meta_tag_id' => Yii::t('bridge', 'Meta Tag ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetaTag()
    {
        return $this->hasOne(MetaTag::class, ['id' => 'meta_tag_id']);
    }

    /**
     * @inheritdoc
     * @return MetaModelQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MetaModelQuery(get_called_class());
    }

    /**
     * Получаем мета-теги
     * Если его не существует, то создаем его, с параметрами по-умолчанию
     *
     * @param ActiveRecord $model
     * @param $metaTagBehaviorName
     * @return MetaTagTranslation|false
     */
    public static function getOrCreate(ActiveRecord $model, string $metaTagBehaviorName)
    {
        $metaTagTranslation = MetaTagTranslation::find()
            ->joinWith('metaTag.metaModel', false)
            ->where([
                'meta_models.model' => get_class($model),
                'meta_models.model_id' => $model->primaryKey,
                'meta_tag_translations.lang' => Yii::$app->language
            ])
            ->one();

        return $metaTagTranslation ?? self::create($model, $metaTagBehaviorName);
    }

    /**
     * Создаем новый объект класса MetaModel,
     * который связывает вызываемую модель ($this->owner) с MetaTag
     *
     * @param ActiveRecord $model
     * @param $metaTagBehaviorName
     * @return MetaTagTranslation|false
     */
    public static function create(ActiveRecord $model, $metaTagBehaviorName)
    {
        $metaTagBehavior = $model->getBehavior($metaTagBehaviorName);
        $modelClassName = get_class($model);
        $modelId = $model->primaryKey;

        if (!$metaTagBehavior || (get_class($metaTagBehavior) !== MetaTagBehavior::class)) {
            throw new InvalidArgumentException('Вы не указали поведение MetaTagBehavior в модели ' . $modelClassName);
        }

        $title = ArrayHelper::getValue($model, $metaTagBehavior->titleColumn, Yii::$app->name);
        $description = ArrayHelper::getValue($model, $metaTagBehavior->descriptionColumn, Yii::$app->name);

        $metaModel = self::find()
            ->where([
                'meta_models.model' => $modelClassName,
                'meta_models.model_id' => $modelId,
            ])
            ->one();

        if (!is_null($metaModel)) {
            $metaTagTranslation = new MetaTagTranslation([
                'meta_tag_id' => $metaModel->meta_tag_id,
                'lang' => Yii::$app->language,
                'title' => $title,
                'description' => $description
            ]);

            return $metaTagTranslation->save() ? $metaTagTranslation : false;
        }

        $defaultParams = [];

        foreach (Yii::$app->urlManager->languages as $label => $code) {
            $defaultParams[$code] = [
                'lang' => $code,
                'title' => $title,
                'description' => $description
            ];
        }

        $metaTag = MetaTag::create($defaultParams);

        if (!$metaTag) {
            return false;
        }

        $metaModel = new self([
            'meta_tag_id' => $metaTag->primaryKey,
            'model' => $modelClassName,
            'model_id' => $modelId,
        ]);

        return $metaModel->save() ? $metaTag->translation : false;
    }
}
