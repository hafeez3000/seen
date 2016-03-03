<?php namespace app\modules\admin\models;

use \yii\db\ActiveRecord;

use \app\models\User;

class AuthItem extends ActiveRecord
{
	/**
	* @inheritdoc
	*/
	public static function tableName()
	{
		return '{{%auth_item}}';
	}

	/**
	 * Get users which are connected to the auth item.
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUsers()
	{
		return $this->hasMany(User::className(), ['id' => 'user_id'])
			->viaTable('{{%auth_assignment}}', ['item_name' => 'name']);
	}

	public function getUserCount()
	{
		return $this->getUsers()->count();
	}
}
