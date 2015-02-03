<?php namespace app\models\forms;

use \Yii;
use \yii\base\Model;

use \app\models\User;
use \app\models\Language;

/**
 * SignupForm is the model behind the sign up form.
 */
class AccountForm extends Model
{
	public $email;
	public $name;
	public $language;
	public $timezone;
	public $profile_public;
	public $profile_name;
	public $password;

	/**
	 *	Define validation rules.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[['email'], 'required'],
			[['email'], 'email'],
			[['email', 'name'], 'string', 'max' => 100],
			[['timezone'], 'timezoneExists'],
			[['language'], 'exist', 'targetClass' => Language::className(), 'targetAttribute' => 'id'],
			[['profile_public'], 'boolean'],
			[['password'], 'string', 'min' => 6],
			[['email'], 'unique', 'targetClass' => User::className(), 'targetAttribute' => 'email'],
		];
	}

	public function attributeLabels()
	{
		return [
			'email' => Yii::t('User/Account', 'Email'),
			'name' => Yii::t('User/Account', 'Name'),
			'profile_public' => Yii::t('User/Account', 'Public profile'),
			'password' => Yii::t('User/Account', 'Password (only change to set a new one)'),
		];
	}

	public function __construct(User $user)
	{
		$this->email = $user->email;
		$this->name = $user->name;
		$this->language = $user->language_id;

		$timezones = $this->timezones;
		$this->timezone = array_search($user->timezone, $timezones);

		$this->profile_public = $user->profile_public;
		$this->profile_name = $user->profile_name;
	}

	public function timezoneExists($attribute)
	{
		$timezones = $this->timezones;

		if (!isset($timezones[$attribute]))
			$this->addError($attribute, Yii::t('User/Account', 'The timezone does not exist!'));
	}

	public function getLanguages()
	{
		$languages = Language::find()
			->orderBy(['name' => SORT_ASC])
			->asArray()
			->all();
		$items = [];

		foreach ($languages as $language) {
			$items += [$language['id'] => $language['name']];
		}

		return $items;
	}

	public function getTimezones()
	{
		$tza = \DateTimeZone::listAbbreviations();
		$tzlist = [];
		foreach ($tza as $zone)
			foreach ($zone as $item)
				if (is_string($item['timezone_id']) && $item['timezone_id'] != '')
					$tzlist[] = $item['timezone_id'];

		$tzlist = array_unique($tzlist);
		asort($tzlist);

		return array_values($tzlist);
	}

	/**
	 * Logs in a user using the provided email and password.
	 *
	 * @return boolean whether the user is logged in successfully
	 */
	public function save()
	{
		$user = Yii::$app->user->identity;
		$user->scenario = 'account';

		$timezones = $this->timezones;

		$user->email = $this->email;
		$user->name = $this->name;
		$user->language_id = $this->language;
		$user->timezone = $timezones[$this->timezone];
		$user->profile_public = $this->profile_public;

		if (!empty($this->password))
			$user->setPassword($this->password);

		return $user->save();
	}
}
