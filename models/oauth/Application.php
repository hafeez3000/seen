<?php namespace app\models\oauth;

use \Yii;
use \yii\db\ActiveRecord;
use \yii\helpers\Security;

use \app\components\TimestampBehavior;
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
	 * Read access to watched movies and tv shows.
	 */
	const SCOPE_READONLY = 'read';

	/**
	 * Update account settings like the language (not the email and password).
	 */
	const SCOPE_ACCOUNT = 'account';

	/**
	 * Write access to movies.
	 */
	const SCOPE_MOVIES = 'movies';

	/**
	 * Write access to tv shows.
	 */
	const SCOPE_TV_SHOWS = 'tv_shows';

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
			[['user_id', 'name', 'callback'], 'required'],
			[['user_id'], 'integer'],
			[['description'], 'string'],
			[['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
			[['name'], 'string', 'max' => 100],
			[['description', 'website', 'callback'], 'string', 'max' => 255],
			[['key', 'secret'], 'string', 'max' => 64],
			[['key', 'secret'], 'unique'],
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
	 * @inheritdoc
	 */
	public function scenarios()
	{
		$scenarios = parent::scenarios();

		$scenarios['create'] = ['name', 'description', 'website', 'callback',];
		$scenarios['update'] = ['name', 'description', 'website', 'callback',];

		return $scenarios;
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
			'key' => Yii::t('Oauth/Application', 'Client ID'),
			'secret' => Yii::t('Oauth/Application', 'Secret'),
			'callback' => Yii::t('Oauth/Application', 'Callback URL'),
			'created_at' => Yii::t('Oauth/Application', 'Created at'),
			'updated_at' => Yii::t('Oauth/Application', 'Updated at'),
		];
	}

	public function beforeValidate()
	{
		if ($this->scenario == 'create') {
			$this->user_id = Yii::$app->user->id;

			$this->regenerate();
		}

		return parent::beforeValidate();
	}

	public function regenerate()
	{
		$double = true;
		while ($double) {
			$this->key = strtolower(Security::generateRandomKey(32));
			$double = Application::find()
				->where(['key' => $this->key])
				->exists();
		}

		$double = true;
		while ($double) {
			$this->secret = strtolower(Security::generateRandomKey(32));
			$double = Application::find()
				->where(['secret' => $this->secret])
				->exists();
		}

		return true;
	}

	public static function scopes()
	{
		return [
			self::SCOPE_READONLY => [
				'name' => Yii::t('Scope', 'Readonly'),
				'description' => Yii::t('Scope', 'Read your account data like email, language and your watched tv shows and movies.'),
			],
			self::SCOPE_ACCOUNT => [
				'name' => Yii::t('Scope', 'Account'),
				'description' => Yii::t('Scope', 'Update your account settings like your language.'),
			],
			self::SCOPE_TV_SHOWS => [
				'name' => Yii::t('Scope', 'TV Shows'),
				'description' => Yii::t('Scope', 'Label episodes as seen and subscribe to tv shows.'),
			],
			self::SCOPE_MOVIES => [
				'name' => Yii::t('Scope', 'Movies'),
				'description' => Yii::t('Scope', 'Label movies as seen.'),
			],
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
