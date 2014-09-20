<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;
use \yii\web\IdentityInterface;

use \app\components\TimestampBehavior;
use \app\components\Email;

/**
 * Model class for Users.
 *
 * @property integer $id
 * @property string $email
 * @property string $name
 * @property string $password
 * @property integer $language_id
 * @property string $timezone
 * @property string $reset_key
 * @property string $validation_key
 * @property string $api_key
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Show[] $shows
 * @property Language $language
 */
class User extends ActiveRecord implements IdentityInterface
{
	const EVENT_AFTER_REGISTER = 'afterRegister';

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['email', 'password'], 'required'],
			[['email'], 'email'],
			[['email', 'name', 'timezone'], 'string', 'max' => 100],
			[['language_id'], 'integer'],
			[['reset_key', 'validation_key'], 'string', 'max' => 75],
			[['api_key'], 'string', 'max' => 32],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'Y-m-d H:i:s']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('User', 'ID'),
			'email' => Yii::t('User', 'Email'),
			'name' => Yii::t('User', 'Name'),
			'password' => Yii::t('User', 'Password'),
			'language_id' => Yii::t('User', 'Language'),
			'timezone' => Yii::t('User', 'Timezone'),
			'reset_key' => Yii::t('User', 'Reset key'),
			'validation_key' => Yii::t('User', 'Validation key'),
			'created_at' => Yii::t('User', 'Created at'),
			'updated_at' => Yii::t('User', 'Updated at'),
			'deleted_at' => Yii::t('User', 'Deleted at'),
		];
	}

	/**
	 * @inheritdoc
	 */
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

		$scenarios['register'] = ['email', 'password'];
		$scenarios['account'] = ['email', 'name', 'language_id', 'timezone', 'password'];

		return $scenarios;
	}

	/**
	 * @inheritdoc
	 */
	public function fields()
	{
		return [
			'id',
			'email',
			'name',
			'language' => function() {
				return $this->language->iso;
			},
			'timezone',
		];
	}

	public static function salt()
	{
		return Yii::$app->params['salt'];
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentity($id)
	{
		return self::findOne($id);
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		return null;
	}

	/**
	 * Finds user by email.
	 *
	 * @param string $email
	 *
	 * @return User|null
	 */
	public static function findByEmail($email)
	{
		return self::findOne([
			'email' => $email,
		]);
	}

	/**
	 * Finds user by reset key.
	 *
	 * @param string $token Reset key
	 *
	 * @return User|null
	 */
	public static function findByResetKey($token)
	{
		return self::findOne([
			'reset_key' => $token,
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey()
	{
		return $this->api_key;
	}

	/**
	 * @inheritdoc
	 */
	public function validateAuthKey($authKey)
	{
		return $this->api_key === $authKey;
	}

	/**
	 * After save method.
	 *
	 * Sends welcome email and register user at mailchimp.
	 *
	 * @param boolean $insert
	 *
	 * @access public
	 * @return void
	 */
	public function afterSave($insert)
	{
		if ($insert && $this->scenario == 'register') {
			$this->trigger(self::EVENT_AFTER_REGISTER);
		}

		return parent::afterSave($insert);
	}

		/**
	 * Generate Salt with CRYPT_BLOWFISH
	 *
	 * @access private
	 * @return string Salt
	 */
	private function generateSalt() {
		$chars = './abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$charCount = strlen($chars);
		$salt = '$2a$10$';

		for ($i = 0; $i < 20; $i++) {
			$salt .= substr($chars, rand(0, $charCount-1), 1);
		}

		$salt .= '$';

		return $salt;
	}

	/**
	 * Encrypt password with salts.
	 *
	 * @param string $password
	 *
	 * @access public
	 * @return string Encrypted password
	 */
	public function encryptPassword($password) {
		return crypt(crypt($password, self::salt()), $this->generateSalt());
	}

	/**
	 * Get the random salt from the hash.
	 *
	 * @param string $hash
	 *
	 * @access private
	 * @return string Salt
	 */
	private function decryptSalt($hash) {
		return substr($hash, 0, 29);
	}

	/**
	 * Set a new password for this user.
	 *
	 * @access public
	 * @return void
	 */
	public function setPassword($password)
	{
		$this->password = $this->encryptPassword($password);
	}

	/**
	 * Validates the password.
	 *
	 * @param string $password password to validate
	 *
	 * @return boolean if password provided is valid for current user
	 */
	public function validatePassword($password)
	{
		return (crypt(crypt($password, self::salt()), $this->decryptSalt($this->password)) == $this->password);
	}

	public function generateResetKey()
	{
		$this->reset_key = md5($this->id . time() . $this->generateSalt());
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getShows()
	{
		return $this->hasMany(Show::className(), ['id' => 'show_id'])
			->viaTable('{{%user_show}}', ['user_id' => 'id'], function($query) {
				$query
					->where(['{{%user_show}}.[[archived]]' => 0])
					->andWhere(['{{%user_show}}.[[deleted_at]]' => null]);
			});
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getArchivedShows()
	{
		return $this->hasMany(Show::className(), ['id' => 'show_id'])
			->viaTable('{{%user_show}}', ['user_id' => 'id'], function($query) {
				$query->where(['{{%user_show}}.[[archived]]' => 1]);
			});
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAllShows()
	{
		return $this->hasMany(Show::className(), ['id' => 'show_id'])
			->viaTable('{{%user_show}}', ['user_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMovies()
	{
		return $this->hasMany(Movie::className(), ['id' => 'movie_id'])
			->viaTable('{{%user_movie}}', ['user_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLanguage()
	{
		return $this->hasOne(Language::className(), ['id' => 'language_id']);
	}

	/**
	 * Get user episodes for this user.
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUserEpisodes()
	{
		return Episode::find()
			->distinct()
			->select('{{%user_episode}}.*')
			->from([
				'{{%user_episode}}',
				'{{%user_show_run}}',
			])
			->where(['{{%user_show_run}}.[[user_id]]' => $this->id])
			->andWhere('{{%user_episode}}.[[run_id]] = {{%user_show_run}}.[[id]]');
	}

	/**
	 * Get episodes seen by this user.
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getEpisodes()
	{
		return Episode::find()
			->distinct()
			->select('{{%episode}}.*')
			->from([
				'{{%episode}}',
				'{{%user_episode}}',
				'{{%user_show_run}}',
			])
			->where(['{{%user_show_run}}.[[user_id]]' => $this->id])
			->andWhere('{{%user_episode}}.[[run_id]] = {{%user_show_run}}.[[id]]')
			->andWhere('{{%episode}}.[[id]] = {{%user_episode}}.[[episode_id]]');
	}
}
