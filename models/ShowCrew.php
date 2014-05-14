<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;
use \app\components\PersonTrait;

/**
 * This is the model class for the Show Crew.
 *
 * @property integer $id
 * @property integer $show_id
 * @property integer $person_id
 * @property string $department
 * @property string $job
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Show $show
 * @property Person $person
 */
class ShowCrew extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%show_crew}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['show_id', 'person_id'], 'required'],
			[['id', 'show_id', 'person_id'], 'integer'],
			[['created_at', 'updated_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['department', 'job'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Show/Crew', 'ID'),
			'show_id' => Yii::t('Show/Crew', 'Show'),
			'person_id' => Yii::t('Show/Crew', 'Person'),
			'department' => Yii::t('Show/Crew', 'Department'),
			'job' => Yii::t('Show/Crew', 'Job'),
			'created_at' => Yii::t('Show/Crew', 'Created at'),
			'updated_at' => Yii::t('Show/Crew', 'Updated at'),
		];
	}

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
				],
			],
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getShow()
	{
		return $this->hasOne(Show::className(), ['id' => 'show_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPerson()
	{
		return $this->hasOne(Person::className(), ['id' => 'person_id']);
	}
}
