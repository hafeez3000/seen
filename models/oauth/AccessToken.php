<?php namespace app\models\oauth;

use \Yii;
use \yii\db\ActiveRecord;
use \yii\helpers\Security;

use \app\components\TimestampBehavior;
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
	const EXPIRES_IN = 86400;

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
			[['expires_at', 'created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
			[['access_token'], 'string', 'max' => 32]
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

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
				],
			],
		];
	}

	public function beforeValidate()
	{
		if ($this->isNewRecord) {
			$double = true;
			while ($double) {
				$this->access_token = strtolower(Security::generateRandomKey(32));
				$double = AccessToken::find()
					->where(['access_token' => $this->access_token])
					->exists();
			}
		}

		return parent::beforeValidate();
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
