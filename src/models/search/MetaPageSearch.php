<?php

namespace Bridge\Core\Models\Search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use Bridge\Core\Models\MetaPage;

/**
 * MetaPageSearch represents the model behind the search form of `Bridge\Core\Models\MetaPage`.
 */
class MetaPageSearch extends MetaPage
{
    /**
     * @var string $title
     */
    public $title;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'meta_tag_id'], 'integer'],
            [['module', 'controller', 'action', 'title'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MetaPage::find()->joinWith('metaTag.metaTagTranslations')->where(['meta_tag_translations.lang' => Yii::$app->language]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['title'] = [
            'asc' => ['meta_tag_translations.title' => SORT_ASC],
            'desc' => ['meta_tag_translations.title' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'meta_tag_id' => $this->meta_tag_id,
        ]);

        $query->andFilterWhere(['like', 'module', $this->module])
            ->andFilterWhere(['like', 'controller', $this->controller])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'meta_tag_translations.title', $this->title]);

        return $dataProvider;
    }
}
