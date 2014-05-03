<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for TV Show Seasons.
 *
 * @property integer $id
 * @property integer $themoviedb_id
 * @property integer $show_id
 * @property string $name
 * @property integer $number
 * @property string $overview
 * @property string $poster_path
 * @property string $air_date
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Episode[] $episodes
 * @property Show $show
 */
class Season extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%season}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['show_id', 'number'], 'required'],
			[['themoviedb_id', 'show_id', 'number'], 'integer'],
			[['overview'], 'string'],
			[['air_date', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['name', 'poster_path'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Season', 'ID'),
			'themoviedb_id' => Yii::t('Season', 'TheMovieDB'),
			'show_id' => Yii::t('Season', 'Show'),
			'name' => Yii::t('Season', 'Name'),
			'number' => Yii::t('Season', 'Number'),
			'overview' => Yii::t('Season', 'Overview'),
			'poster_path' => Yii::t('Season', 'Poster path'),
			'air_date' => Yii::t('Season', 'Air date'),
			'created_at' => Yii::t('Season', 'Created at'),
			'updated_at' => Yii::t('Season', 'Updated at'),
			'deleted_at' => Yii::t('Season', 'Deleted at'),
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
					ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
				],
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function fields()
	{
		return [
			'id' => 'themoviedb_id',
			'number',
			'name',
			'overview',
			'poster_path',
			'air_date',
			'last_update' => 'updated_at',
			'episodes',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEpisodes()
	{
		return $this->hasMany(Episode::className(), ['season_id' => 'id'])
			->orderBy(['{{%episode}}.[[number]]' => SORT_ASC]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getShow()
	{
		return $this->hasOne(Show::className(), ['id' => 'show_id']);
	}

	public function getLatestUserEpisodesCount()
	{
		$result = Yii::$app->db->createCommand('
			SELECT
				COUNT(DISTINCT {{%user_episode}}.[[id]]) AS [[count]]
			FROM
				{{%episode}},
				{{%user_episode}}
			WHERE
				{{%user_episode}}.[[run_id]] = (
					SELECT
						{{%user_show_run}}.[[id]]
					FROM
						{{%user_show_run}}
					WHERE
						{{%user_show_run}}.[[user_id]] = :user_id AND
						{{%user_show_run}}.[[show_id]] = :show_id
					ORDER BY
						{{%user_show_run}}.[[created_at]] DESC
					LIMIT 1
				) AND
				{{%user_episode}}.[[episode_id]] IN (
					SELECT
						{{episode}}.[[id]]
					FROM
						{{%episode}} AS {{episode}}
					WHERE
						{{episode}}.season_id = :season_id
				)
		')
			->bindValue(':user_id', Yii::$app->user->id)
			->bindValue(':show_id', $this->show_id)
			->bindValue(':season_id', $this->id)
			->queryOne();

		return $result['count'];
	}

	public function getFullName()
	{
		if (!empty($this->name))
			return $this->name;
		else
			return Yii::t('Season', 'Season {number}', ['number' => $this->number]);
	}
}
