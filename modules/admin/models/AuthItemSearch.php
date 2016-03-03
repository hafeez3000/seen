<?php namespace app\modules\admin\models;

use \yii\data\ActiveDataProvider;

class AuthItemSearch extends AuthItem
{
	public $user_count;

	/**
	* @inheritdoc
	*/
	public function rules()
	{
		return [
			[['name'], 'safe'],
			[['user_count'], 'integer'],
		];
	}

	/**
	* Filter auth items.
	*
	* @param array $params
	*
	* @return \yii\data\ActiveDataProvider
	*/
	public function search($params)
	{
		$query = AuthItem::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere(['like', AuthItem::tableName() . '.[[name]]', $this->name]);

		return $dataProvider;
	}
}
