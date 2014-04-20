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
 * @property string $password
 * @property integer $language_id
 * @property integer $level
 * @property string $reset_key
 * @property string $validation_key
 * @property string $api_key
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Show[] $shows
 */
class User extends ActiveRecord implements IdentityInterface
{

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
			[['email'], 'string', 'max' => 100],
			[['language_id', 'level'], 'integer'],
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
			'password' => Yii::t('User', 'Password'),
			'language_id' => Yii::t('User', 'Language'),
			'level' => Yii::t('User', 'Level'),
			'reset_key' => Yii::t('User', 'Reset key'),
			'validation_key' => Yii::t('User', 'Validation key'),
			'api_key' => Yii::t('User', 'API key'),
			'created_at' => Yii::t('User', 'Created at'),
			'updated_at' => Yii::t('User', 'Updated at'),
			'deleted_at' => Yii::t('User', 'Deleted at'),
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
	public static function findIdentity($id)
	{
		return self::findOne($id);
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token)
	{
		return self::findOne([
			'api_key' => $token,
		]);
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
	 * @access public
	 * @return void
	 */
	public function afterSave()
	{
		if ($this->scenario == 'register') {
			//Send welcome email
			$email = new Email;
			$email->to = $this->email;
			$email->subject = Yii::t('Email/Register', 'Welcome to SEEN');

			$html = Yii::t('Email/Register', '<h1 class="h1">Welcome to <span class="highlight">SEEN</span></h1>');
			$html .= Yii::t(
				'Email/Register',
				'<p>You successfully registered at <a href="{url}">seenapp.com</a>! Start now by subscribing to your <a href="{url}">favorite tv shows</a>.</p>',
				array(
					'{url}' => Yii::app()->createAbsoluteUrl('/'),
				)
			);

			$email->send(
				'Default',
				array(
					array(
						'name' => 'content',
						'content' => $html,
					)
				),
				array(
					'register',
				)
			);

			//Register user to mailchimp
			$mc = new Mailchimp();
			$mc->subscribe($this->email);
		}

		return parent::afterSave();
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
	 * @access private
	 * @return string Encrypted password
	 */
	private function encryptPassword($password) {
		return crypt(crypt($password, self::SALT), $this->generateSalt());
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
		return (crypt(crypt($password, self::SALT), $this->decryptSalt($this->password)) == $this->password);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getShows()
	{
		return $this->hasMany(Show::className(), ['id' => 'show_id'])
			->viaTable('{{%user_show}}', ['user_id' => 'id'], function($query) {
				$query->where(['{{%user_show}}.[[archived]]' => 0]);
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
}
