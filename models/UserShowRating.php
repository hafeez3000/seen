<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for tv show ratings from a User.
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $themoviedb_id
 * @property integer $rating
 * @property bool $sync
 *
 * @property User $user
 */
class UserShowRating extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_show_rating}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'themoviedb_id', 'rating'], 'required'],
			[['user_id', 'themoviedb_id', 'rating'], 'integer'],
			[['sync'], 'boolean'],
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
