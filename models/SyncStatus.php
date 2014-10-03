<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for the Sync Status.
 *
 * @property string $name
 * @property string $updated
 * @property string $value
 */
class SyncStatus extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%sync_status}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name'], 'required'],
			[['updated'], 'date', 'format' => 'php:Y-m-d'],
			[['name'], 'string', 'max' => 255],
			[['value'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'name' => Yii::t('SyncStatus', 'Name'),
			'updated' => Yii::t('SyncStatus', 'Updated'),
			'value' => Yii::t('SyncStatus', 'Value'),
		];
	}
}
