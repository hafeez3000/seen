<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for Email attachments.
 *
 * @property string $id
 * @property string $email_id
 * @property string $name
 * @property string $type
 *
 * @property Email $email
 */
class EmailAttachment extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%email_attachment}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['email_id', 'name'], 'required'],
			[['email_id'], 'integer'],
			[['name', 'type'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Email/Attachment', 'ID'),
			'email_id' => Yii::t('Email/Attachment', 'Email'),
			'name' => Yii::t('Email/Attachment', 'Name'),
			'type' => Yii::t('Email/Attachment', 'Type'),
		];
	}

	public function getFilename()
	{
		return Yii::$app->basePath . '/upload/email/' . $this->id;
	}

	public function saveAttachment($content, $base64)
	{
		// Atachment has to be saved because the ID is needed
		if ($this->isNewRecord)
			return false;

		if ($base64)
			return (file_put_contents($this->filename, base64_decode($content)) !== false);
		else
			return (file_put_contents($this->filename, $content) !== false);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEmail()
	{
		return $this->hasOne(Email::className(), ['id' => 'email_id']);
	}
}
