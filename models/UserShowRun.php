<?php namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for TV Show Runs.
 *
 * @property string $id
 * @property string $user_id
 * @property string $show_id
 * @property string $created_at
 *
 * @property UserEpisode[] $userEpisodes
 * @property Show $show
 * @property User $user
 */
class UserShowRun extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_show_run}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'show_id'], 'required'],
			[['user_id', 'show_id'], 'integer'],
			[['created_at'], 'date', 'format' => 'php:Y-m-d H:i:s']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('User/Show/Run', 'ID'),
			'user_id' => Yii::t('User/Show/Run', 'User'),
			'show_id' => Yii::t('User/Show/Run', 'Show'),
			'created_at' => Yii::t('User/Show/Run', 'Created at'),
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
	public function getUserEpisodes()
	{
		return $this->hasMany(UserEpisode::className(), ['run_id' => 'id']);
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
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
