<?php namespace app\models\oauth;

use \Yii;
use \yii\db\ActiveRecord;
use \yii\helpers\Security;

use \app\components\TimestampBehavior;
use \app\models\User;

/**
 * This is the model class for Oauth refresh tokens.
 *
 * @property string $refresh_token
 * @property integer $user_id
 * @property integer $oauth_application_id
 * @property string $scopes
 * @property string $created_at
 * @property string $updated_at
 *
 * @property OauthApplication $oauthApplication
 * @property User $user
 */
class RefreshToken extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%oauth_refresh_token}}';
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
			[['created_at', 'updated_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['refresh_token'], 'string', 'max' => 32]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'refresh_token' => Yii::t('Oauth/RefreshToken', 'Refresh token'),
			'user_id' => Yii::t('Oauth/RefreshToken', 'User'),
			'oauth_application_id' => Yii::t('Oauth/RefreshToken', 'Oauth application'),
			'scopes' => Yii::t('Oauth/RefreshToken', 'Scopes'),
			'created_at' => Yii::t('Oauth/RefreshToken', 'Created at'),
			'updated_at' => Yii::t('Oauth/RefreshToken', 'Updated at'),
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
				$this->refresh_token = strtolower(Security::generateRandomKey(32));
				$double = RefreshToken::find()
					->where(['refresh_token' => $this->refresh_token])
					->exists();
			}
		}

		return parent::beforeValidate();
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOauthApplication()
	{
		return $this->hasOne(OauthApplication::className(), ['id' => 'oauth_application_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
