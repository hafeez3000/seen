<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;
use \Carbon\Carbon;

use \app\components\TimestampBehavior;

/**
 * This is the model class for Episodes seen by a User.
 *
 * @property integer $id
 * @property integer $episode_id
 * @property integer $run_id
 * @property string $created_at
 *
 * @property Episode $episode
 * @property UserShowRun $run
 */
class UserEpisode extends ActiveRecord
{
	public $number;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_episode}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['run_id', 'episode_id'], 'required'],
			[['run_id', 'episode_id'], 'integer'],
			[['created_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('User/Episode', 'ID'),
			'run_id' => Yii::t('User/Episode', 'Run'),
			'episode_id' => Yii::t('User/Episode', 'Episode'),
			'created_at' => Yii::t('User/Episode', 'Created at'),
		];
	}

	/**
	 * @inheritdoc
	 */
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
	 * @inheritdoc
	 */
	public function fields()
	{
		if (Yii::$app->id == 'api') {
			return [
				'id' => 'episode_id',
				'run' => 'run_id',
				'watched' => 'created_at',
			];
		}

		return [
			'id',
			'run_id',
			'episode_id',
			'created_at',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEpisode()
	{
		return $this->hasOne(Episode::className(), ['id' => 'episode_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRun()
	{
		return $this->hasOne(UserShowRun::className(), ['id' => 'run_id']);
	}

	public function getCreatedAtAgo()
	{
		$created = new Carbon($this->created_at);
		return $created->diffForHumans();
	}
}
