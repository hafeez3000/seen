<?php namespace app\models\forms;

use \Yii;
use \yii\base\Model;

/**
 * ImportForm is the model behind the user data import.
 */
class ImportForm extends Model
{
	const TYPE_FOUNDD = 'foundd';

	public $type;
	public $file;

	/**
	 *	Define validation rules.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[['type', 'file'], 'required'],
			[['type'], 'isImportType'],
			[['file'], 'file', 'extensions' => ['json']],
		];
	}

	public function isImportType($attribute)
	{
		if ($this->$attribute != self::TYPE_FOUNDD)
			$this->addError($attribute, Yii::t('Import/Form', '{type} is not a valid type!', ['type' => $this->$attribute]));
	}

	/**
	 * Saves the file in the correct location.
	 *
	 * @return void
	 */
	public function upload()
	{
		return $this->file->saveAs(Yii::$app->basePath . '/upload/import/' . Yii::$app->user->id . '-' . $this->type . '.json');
	}
}
