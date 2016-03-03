<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for Person Aliases.
 *
 * @property integer $id
 * @property integer $person_id
 * @property string $alias
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Person $person
 */
class PersonAlias extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%person_alias}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['person_id', 'alias'], 'required'],
			[['person_id'], 'integer'],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
			[['alias'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Person/Alias', 'ID'),
			'person_id' => Yii::t('Person/Alias', 'Person'),
			'alias' => Yii::t('Person/Alias', 'Alias'),
			'created_at' => Yii::t('Person/Alias', 'Created at'),
			'updated_at' => Yii::t('Person/Alias', 'Updated at'),
			'deleted_at' => Yii::t('Person/Alias', 'Deleted at'),
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
	 * @return \yii\db\ActiveQuery
	 */
	public function getPerson()
	{
		return $this->hasOne(Person::className(), ['id' => 'person_id']);
	}
}
