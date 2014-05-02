<?php namespace app\models\oauth;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;
use \app\models\User;

/**
 * This is the model class for Oauth request tokens.
 *
 * @property string $request_token
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
class RequestToken extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%oauth_request_token}}';
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
			[['expires_at', 'created_at', 'updated_at'], 'safe'],
			[['request_token'], 'string', 'max' => 32]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'request_token' => Yii::t('Oauth/RequestToken', 'Request token'),
			'user_id' => Yii::t('Oauth/RequestToken', 'User'),
			'oauth_application_id' => Yii::t('Oauth/RequestToken', 'Oauth application'),
			'scopes' => Yii::t('Oauth/RequestToken', 'Scopes'),
			'expires_at' => Yii::t('Oauth/RequestToken', 'Expires at'),
			'created_at' => Yii::t('Oauth/RequestToken', 'Created at'),
			'updated_at' => Yii::t('Oauth/RequestToken', 'Updated at'),
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
