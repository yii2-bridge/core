<?php

namespace naffiq\bridge\models;

use naffiq\bridge\models\query\MetaPageQuery;
use Yii;

/**
 * This is the model class for table "meta_pages".
 *
 * @property integer $id
 * @property integer $meta_tag_id
 * @property string $module
 * @property string $controller
 * @property string $action
 *
 * @property MetaTag $metaTag
 */
class MetaPage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meta_pages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meta_tag_id', 'module', 'controller', 'action'], 'required'],
            [['meta_tag_id'], 'integer'],
            [['module', 'controller', 'action'], 'string', 'max' => 255],
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
            'meta_tag_id' => Yii::t('bridge', 'Meta Tag ID'),
            'module' => Yii::t('bridge', 'Module'),
            'controller' => Yii::t('bridge', 'Controller'),
            'action' => Yii::t('bridge', 'Action'),
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
     * @return MetaPageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MetaPageQuery(get_called_class());
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        /**
         * Сохранение мета-тегов
         */
        $this->metaTag->save();
    }

    /**
     * Получаем объект класса MetaPage
     * Если его не существует, то создаем его, с параметрами по-умолчанию
     *
     * Пример для значении по-умолчанию:
     * [
     *  'en-US' => [
     *      'lang' => 'en-US',
     *      'title' => 'Title'
     *  ],
     *  'ru-RU' => [
     *      'lang' => 'ru-RU',
     *      'title' => 'Заголовок'
     *      ],
     *  'kk-KZ' => [
     *      'lang' => 'kk-KZ',
     *      'title' => 'Тақырып'
     *  ]
     * ]
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param array $defaultParams
     * @return MetaPage
     */
    public static function getOrCreate($module, $controller, $action, $defaultParams = [])
    {
        $metaPage = self::findOne(['module' => $module, 'controller' => $controller, 'action' => $action]);

        return $metaPage ?? self::create($module, $controller, $action, $defaultParams);
    }

    /**
     * Создаем объект класса MetaPage, с параметрами по-умолчанию
     *
     * @param $module
     * @param $controller
     * @param $action
     * @param array $defaultParams
     * @return MetaPage
     */
    private static function create($module, $controller, $action, $defaultParams = [])
    {
        $metaTag = MetaTag::create($defaultParams);

        $metaPage = new MetaPage();
        $metaPage->meta_tag_id = $metaTag->id;
        $metaPage->module = $module;
        $metaPage->controller = $controller;
        $metaPage->action = $action;
        $metaPage->save();

        return $metaPage;
    }
}
