<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for Email groups.
 *
 * @property integer $id
 * @property string $name
 * @property string $receiver
 *
 * @property User[] $users
 */
class EmailGroup extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%email_group}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name', 'receiver'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Email/Group', 'ID'),
			'name' => Yii::t('Email/Group', 'Name'),
			'receiver' => Yii::t('Email/Group', 'Receiver'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUsers()
	{
		return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('{{%user_email_group}}', ['email_group_id' => 'id']);
	}
}
