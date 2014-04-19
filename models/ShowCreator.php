<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for Show Creators.
 *
 * @property string $show_id
 * @property string $person_id
 *
 * @property Person $person
 * @property Show $show
 */
class ShowCreator extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%show_creator}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['show_id', 'person_id'], 'required'],
			[['show_id', 'person_id'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'show_id' => Yii::t('Show/Creator', 'Show'),
			'person_id' => Yii::t('Show/Creator', 'Person'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPerson()
	{
		return $this->hasOne(Person::className(), ['id' => 'person_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getShow()
	{
		return $this->hasOne(Show::className(), ['id' => 'show_id']);
	}
}
