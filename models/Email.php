<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for Emails.
 *
 * @property integer $id
 * @property integer $ts
 * @property string $event
 * @property string $text
 * @property string $html
 * @property string $from_email
 * @property string $from_name
 * @property string $subject
 * @property double $spam_score
 * @property boolean $success
 * @property integer $respond_user_id
 * @property string $respond_at
 * @property integer $assigned_user_id
 *
 * @property EmailAttachment[] $attachments
 * @property User $responded
 * @property User $assigned
 * @property EmailTo $to
 */
class Email extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%email}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['ts'], 'safe'],
			[['text', 'html'], 'string'],
			[['spam_score'], 'number'],
			[['event'], 'string', 'max' => 100],
			[['from_email', 'from_name', 'subject'], 'string', 'max' => 255],
			[['success'], 'boolean'],
			[['respond_user_id', 'assigned_user_id'], 'integer'],
			[['respond_at'], 'date', 'format' => 'Y-m-d H:i:s'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Email', 'ID'),
			'ts' => Yii::t('Email', 'Timestamp'),
			'event' => Yii::t('Email', 'Event'),
			'text' => Yii::t('Email', 'Text'),
			'html' => Yii::t('Email', 'Html'),
			'from_email' => Yii::t('Email', 'From (Email)'),
			'from_name' => Yii::t('Email', 'From (Name)'),
			'subject' => Yii::t('Email', 'Subject'),
			'spam_score' => Yii::t('Email', 'Spam score'),
			'success' => Yii::t('Email', 'Success'),
			'respond_user_id' => Yii::t('Email', 'Responded by'),
			'respond_at' => Yii::t('Email', 'Responded at'),
			'assigned_user_id' => Yii::t('Email', 'Assigned user'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAttachments()
	{
		return $this->hasMany(EmailAttachment::className(), ['email_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getGroups()
	{
		return EmailGroup::findBySql('
			SELECT
				{{%email_group}}.*
			FROM
				{{%email}},
				{{%email_group}},
				{{%email_to}}
			WHERE
				{{%email}}.[[id]] = :email_id AND
				{{%email_to}}.[[email_id]] = {{%email}}.[[id]] AND
				{{%email_group}}.[[receiver]] = {{%email_to}}.[[to_email]]

		', [
			':email_id' => $this->id,
		])
			->all();
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTo()
	{
		return $this->hasMany(EmailTo::className(), ['email_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getResponded()
	{
		return $this->hasOne(User::className(), ['id' => 'respond_user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAssigned()
	{
		return $this->hasOne(User::className(), ['id' => 'assigned_user_id']);
	}
}
