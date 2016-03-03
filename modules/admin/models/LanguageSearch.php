<?php namespace app\modules\admin\models;

use \Yii;
use \yii\data\ActiveDataProvider;

use \app\models\Language;

/**
 * Class to search and filter language models
 */
class LanguageSearch extends Language
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['iso', 'name', 'en_name'], 'safe'],
		];
	}

	/**
	 * Filter languages.
	 *
	 * @param array $params
	 *
	 * @return \yii\data\ActiveDataProvider
	 */
	public function search($params)
	{
		$query = Language::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere(['like', Language::tableName() . '.[[iso]]', $this->iso]);
		$query->andFilterWhere(['like', Language::tableName() . '.[[name]]', $this->name]);
		$query->andFilterWhere(['like', Language::tableName() . '.[[en_name]]', $this->en_name]);

		return $dataProvider;
	}
}
