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
	public $password;
	public $language;

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
			[['language'], 'exist', 'targetClass' => Language::className(), 'targetAttribute' => 'id'],
			[['password'], 'string', 'min' => 6],
			[['email'], 'unique', 'targetClass' => User::className(), 'targetAttribute' => 'email'],
		];
	}

	public function attributeLabels()
	{
		return [
			'email' => Yii::t('User/Account', 'Email'),
			'name' => Yii::t('User/Account', 'Name'),
			'password' => Yii::t('User/Account', 'Password (only change to set a new one)'),
		];
	}

	public function __construct(User $user)
	{
		$this->email = $user->email;
		$this->name = $user->name;
		$this->language = $user->language_id;
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

	/**
	 * Logs in a user using the provided email and password.
	 *
	 * @return boolean whether the user is logged in successfully
	 */
	public function save()
	{
		$user = Yii::$app->user->identity;
		$user->scenario = 'account';

		$user->attributes = [
			'email' => $this->email,
			'name' => $this->name,
			'language_id' => $this->language,
		];

		if (!empty($this->password))
			$user->setPassword($this->password);

		return $user->save();
	}
}
