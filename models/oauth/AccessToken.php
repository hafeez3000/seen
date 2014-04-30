<?php namespace app\models\oauth;

use \Yii;
use \yii\db\ActiveRecord;

use \app\models\User;

/**
 * This is the model class for Oauth access tokens.
 *
 * @property string $access_token
 * @property integer $user_id
 * @property integer $oauth_application_id
 * @property string $scopes
 * @property string $expires_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Application $application
 * @property User $user
 */
class AccessToken extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%oauth_access_token}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'oauth_application_id'], 'required'],
			[['user_id', 'oauth_application_id'], 'integer'],
			[['scopes'], 'string'],
			[['expires_at', 'created_at', 'updated_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['access_token'], 'string', 'max' => 64]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'access_token' => Yii::t('Oauth/AccessToken', 'Access token'),
			'user_id' => Yii::t('Oauth/AccessToken', 'User'),
			'oauth_application_id' => Yii::t('Oauth/AccessToken', 'Oauth application'),
			'scopes' => Yii::t('Oauth/AccessToken', 'Scopes'),
			'expires_at' => Yii::t('Oauth/AccessToken', 'Expires at'),
			'created_at' => Yii::t('Oauth/AccessToken', 'Created at'),
			'updated_at' => Yii::t('Oauth/AccessToken', 'Updated at'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getApplication()
	{
		return $this->hasOne(Application::className(), ['id' => 'oauth_application_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
