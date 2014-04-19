<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for the Show Crew.
 *
 * @property integer $id
 * @property integer $show_id
 * @property string $name
 * @property string $department
 * @property string $job
 * @property string $profile_path
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Show $show
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
			[['id', 'show_id'], 'required'],
			[['id', 'show_id'], 'integer'],
			[['created_at', 'updated_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['name', 'department', 'job', 'profile_path'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Show/Crew', 'ID'),
			'show_id' => Yii::t('Show/Crew', 'Show ID'),
			'name' => Yii::t('Show/Crew', 'Name'),
			'department' => Yii::t('Show/Crew', 'Department'),
			'job' => Yii::t('Show/Crew', 'Job'),
			'profile_path' => Yii::t('Show/Crew', 'Profile path'),
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
}
