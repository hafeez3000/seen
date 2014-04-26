<?php namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for Email receivers.
 *
 * @property integer $id
 * @property integer $email_id
 * @property string $to_email
 * @property string $to_name
 *
 * @property Email $email
 */
class EmailTo extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%email_to}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['email_id', 'to_email'], 'required'],
			[['email_id'], 'integer'],
			[['to_email', 'to_name'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('app', 'ID'),
			'email_id' => Yii::t('app', 'Email'),
			'to_email' => Yii::t('app', 'To (Email)'),
			'to_name' => Yii::t('app', 'To (Name)'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEmail()
	{
		return $this->hasOne(Email::className(), ['id' => 'email_id']);
	}
}
