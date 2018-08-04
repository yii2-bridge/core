<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 3/26/18
 * Time: 00:39
 */

namespace naffiq\bridge\widgets;


use dosamigos\grid\behaviors\ResizableColumnsBehavior;
use dosamigos\grid\GridView;
use Bridge\Core\Models\Settings;
use Bridge\Core\Models\SettingsGroup;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii2tech\admin\grid\ActionColumn;

class SettingsGridView extends GridView
{
    /** @var SettingsGroup */
    public $group;

    public $itemView = '@bridge/widgets/views/settings-list_item';

    public $options = ['class' => 'grid-view table-responsive'];
    public $tableOptions = ['class' => 'table'];
    public $behaviors = [
        ResizableColumnsBehavior::class
    ];


    public function getSettingsProvider()
    {
        $settingsQuery = Settings::find();

        $settingsProvider = new ActiveDataProvider([
            'query' => $settingsQuery,
            'pagination' => false
        ]);

        $settingsQuery->andWhere(['group_id' => $this->group ? $this->group->id : null]);

        return $settingsProvider;
    }

    public function init()
    {
        $this->dataProvider = $this->getSettingsProvider();
        $this->setLayout();
        $this->columns = [
            [
                'attribute' => 'title',
                'options' => [
                    'width' => '40%'
                ]
            ],
            [
                'attribute' => 'value',
                'format' => 'raw',
                'value' => function ($data) {
                    /**
                     * @var \Bridge\Core\Models\Settings $data
                     */
                    if ($data->type == Settings::TYPE_IMAGE) {
                        return Html::img($data->getThumbUploadUrl('value', 'preview'), [
                            'class' => 'img-circle img-preview'
                        ]);
                    }

                    if ($data->type == Settings::TYPE_SWITCH) {
                        return $data->value ? \Yii::t('bridge', 'Yes') : \Yii::t('bridge', 'No');
                    }

                    return StringHelper::truncate(strip_tags($data->value), 150);
                },
            ],
            [
                'class' => ActionColumn::class,
                'options' => [
                    'width' => '100px',
                    'class' => 'text-center'
                ],
                'template' => '{update}'
            ],
        ];

        parent::init();
    }

    protected function setLayout()
    {
        $this->layout = self::tag('div', [
            self::tag('div', '{items}', ['class' => 'panel-body-settings'])
        ], ['class' => 'panel']);
    }

    protected static function implodeTags($tags)
    {
        return implode('', $tags);
    }

    protected static function tag($tag, $content = '', $options = [])
    {
        return Html::tag($tag, is_array($content) ? static::implodeTags($content) : $content, $options);
    }
}