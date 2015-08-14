<?php namespace app\modules\admin\models;

use \yii\data\ActiveDataProvider;

class KeySearch extends AuthItem
{
	/**
	* @inheritdoc
	*/
	public function rules()
	{
		return [
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
		$query = Key::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		return $dataProvider;
	}
}
