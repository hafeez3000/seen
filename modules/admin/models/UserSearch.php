<?php namespace app\modules\admin\models;

use \Yii;
use \yii\data\ActiveDataProvider;

use \app\models\User;
use \app\models\Language;

/**
 * Class to search and filter User models
 */
class UserSearch extends User
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id'], 'integer'],
			[['email', 'name', 'language.en_name', 'timezone', 'created_at'], 'safe'],
		];
	}

	public function attributes()
	{
		return array_merge(parent::attributes(), [
			'language.en_name',
		]);
	}

	public function attributeLabels()
	{
		return array_merge(parent::attributeLabels(), [
			'language.en_name' => Yii::t('User', 'Language'),
		]);
	}

	/**
	 * Filter users.
	 *
	 * @param array $params
	 *
	 * @return \yii\data\ActiveDataProvider
	 */
	public function search($params)
	{
		$query = User::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$query->joinWith(['language']);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere(['like', User::tableName() . '.[[id]]', $this->id]);
		$query->andFilterWhere(['like', User::tableName() . '.[[email]]', $this->email]);
		$query->andFilterWhere(['like', User::tableName() . '.[[name]]', $this->name]);
		$query->andFilterWhere(['like', Language::tableName() . '.[[en_name]]', $this->getAttribute('language.en_name')]);
		$query->andFilterWhere(['like', User::tableName() . '.[[timezone]]', $this->timezone]);
		$query->andFilterWhere(['like', User::tableName() . '.[[created_at]]', $this->created_at]);

		$dataProvider->sort->attributes['language.en_name'] = [
			'asc' => ['language.en_name' => SORT_ASC],
			'desc' => ['language.en_name' => SORT_DESC],
		];

		return $dataProvider;
	}
}
