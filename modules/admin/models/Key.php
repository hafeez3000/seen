<?php namespace app\modules\admin\models;

use \yii\db\ActiveRecord;

use \app\models\User;

class Key extends ActiveRecord
{
	/**
	* @inheritdoc
	*/
	public static function tableName()
	{
		return '{{%basic_auth_key}}';
	}

	/**
	 * Get users which are connected to the key.
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	public function generate()
	{
		$this->key = hash('sha512', time() . uniqid());
		$this->user_id = \Yii::$app->user->id;
	}
}
