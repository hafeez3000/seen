<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for Movie Companies.
 *
 * @property integer $movie_id
 * @property integer $company_id
 *
 * @property Company $company
 * @property Movie $movie
 */
class MovieCompany extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%movie_company}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['movie_id', 'company_id'], 'required'],
			[['movie_id', 'company_id'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'movie_id' => Yii::t('Movie/Company', 'Movie'),
			'company_id' => Yii::t('Movie/Company', 'Company'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCompany()
	{
		return $this->hasOne(Company::className(), ['id' => 'company_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMovie()
	{
		return $this->hasOne(Movie::className(), ['id' => 'movie_id']);
	}
}
