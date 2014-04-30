<?php namespace app\models\oauth;

use \Yii;
use \yii\db\ActiveRecord;

use \app\models\User;

/**
 * This is the model class for Oauth applications (consumer).
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $description
 * @property string $website
 * @property string $key
 * @property string $secret
 * @property string $callback
 * @property string $created_at
 * @property string $updated_at
 *
 * @property OauthAccessToken[] $accessTokens
 * @property User $user
 * @property OauthRefreshToken[] $refreshTokens
 * @property OauthRequestToken[] $requestTokens
 */
class Application extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%oauth_application}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id'], 'required'],
			[['user_id'], 'integer'],
			[['description'], 'string'],
			[['created_at', 'updated_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['name', 'website', 'callback'], 'string', 'max' => 255],
			[['key', 'secret'], 'string', 'max' => 64]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Oauth/Application', 'ID'),
			'user_id' => Yii::t('Oauth/Application', 'User'),
			'name' => Yii::t('Oauth/Application', 'Name'),
			'description' => Yii::t('Oauth/Application', 'Description'),
			'website' => Yii::t('Oauth/Application', 'Website'),
			'key' => Yii::t('Oauth/Application', 'Key'),
			'secret' => Yii::t('Oauth/Application', 'Secret'),
			'callback' => Yii::t('Oauth/Application', 'Callback url'),
			'created_at' => Yii::t('Oauth/Application', 'Created at'),
			'updated_at' => Yii::t('Oauth/Application', 'Updated at'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAccessTokens()
	{
		return $this->hasMany(AccessToken::className(), ['oauth_application_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRefreshTokens()
	{
		return $this->hasMany(RefreshToken::className(), ['oauth_application_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRequestTokens()
	{
		return $this->hasMany(RequestToken::className(), ['oauth_application_id' => 'id']);
	}
}
