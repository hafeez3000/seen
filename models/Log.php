<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for Log messages.
 *
 * @property integer $id
 * @property integer $level
 * @property string $category
 * @property integer $log_time
 * @property string $message
 */
class Log extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%log}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['level', 'log_time'], 'integer'],
			[['message'], 'string'],
			[['category'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Log', 'ID'),
			'level' => Yii::t('Log', 'Level'),
			'category' => Yii::t('Log', 'Category'),
			'log_time' => Yii::t('Log', 'Log Time'),
			'message' => Yii::t('Log', 'Message'),
		];
	}

	public function getClass()
	{
		switch ($this->level) {
			case 1: return 'danger';
			case 2: return 'warning';
			case 3: return 'info';
		}
	}
}
