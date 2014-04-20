<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for Episodes.
 *
 * @property integer $id
 * @property integer $themoviedb_id
 * @property integer $season_id
 * @property integer $number
 * @property string $name
 * @property string $overview
 * @property string $air_date
 * @property string $still_path
 * @property double $vote_average
 * @property string $vote_count
 * @property string $production_code
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Language $language
 * @property Season $season
 */
class Episode extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%episode}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['season_id'], 'required'],
			[['themoviedb_id', 'season_id', 'number', 'vote_count'], 'integer'],
			[['overview'], 'string'],
			[['air_date'], 'date', 'format' => 'Y-m-d'],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['vote_average'], 'number'],
			[['name', 'still_path', 'production_code'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Episode', 'ID'),
			'themoviedb_id' => Yii::t('Episode', 'Language'),
			'season_id' => Yii::t('Episode', 'Season'),
			'number' => Yii::t('Episode', 'Number'),
			'name' => Yii::t('Episode', 'Name'),
			'overview' => Yii::t('Episode', 'Overview'),
			'air_date' => Yii::t('Episode', 'Air date'),
			'still_path' => Yii::t('Episode', 'Still path'),
			'vote_average' => Yii::t('Episode', 'Average vote'),
			'vote_count' => Yii::t('Episode', 'Vote count'),
			'production_code' => Yii::t('Episode', 'Production code'),
			'created_at' => Yii::t('Episode', 'Created at'),
			'updated_at' => Yii::t('Episode', 'Updated at'),
			'deleted_at' => Yii::t('Episode', 'Deleted at'),
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
	public function getSeason()
	{
		return $this->hasOne(Season::className(), ['id' => 'season_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUserEpisodes()
	{
		return $this->hasMany(UserEpisode::className(), ['episode_id' => 'id']);
	}

	public function getFullName()
	{
		if (empty($this->name))
			return Yii::t('Episode', 'Episode #{number}', ['number' => $this->number]);
		else
			return Yii::t('Episode', '#{number} {name}', ['number' => $this->number, 'name' => $this->name]);
	}

	public function markSeen()
	{
		$run = UserShowRun::find()
			->where(['user_id' => Yii::$app->user->id])
			->where(['show_id' => $this->season->show_id])
			->orderBy(['created_at' => SORT_DESC])
			->one();
		if ($run === null)
			throw new yii\web\BadRequestHttpException;

		$userEpisode = new UserEpisode;
		$userEpisode->episode_id = $this->id;
		$userEpisode->run_id = $run->id;
		return $userEpisode->save();
	}

	public function markUnseen()
	{
		$run = UserShowRun::find()
			->where(['user_id' => Yii::$app->user->id])
			->where(['show_id' => $this->season->show_id])
			->orderBy(['created_at' => SORT_DESC])
			->one();
		if ($run === null)
			throw new yii\web\BadRequestHttpException;

		$userEpisode = UserEpisode::find()
			->where(['run_id' => $run->id])
			->andWhere(['episode_id' => $this->id])
			->one();
		if ($userEpisode === null)
			throw new yii\web\BadRequestHttpException;

		return $userEpisode->delete();
	}
}
