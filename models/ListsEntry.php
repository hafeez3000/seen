<?php namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

use \app\models\Lists;

/**
 * This is the model class for table "{{%list_entry}}".
 *
 * @property integer $id
 * @property integer $list_id
 * @property integer $type
 * @property integer $themoviedb_id
 * @property string $description
 * @property integer $position
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property List $list
 */
class ListsEntry extends ActiveRecord
{
	const TYPE_MOVIE = 0;
	const TYPE_TV_SHOW = 1;
	const TYPE_PERSON = 2;

	private static $_languageId = null;
	private $_model = null;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%list_entry}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['list_id', 'type', 'themoviedb_id'], 'required'],
			[['list_id', 'type', 'themoviedb_id', 'position'], 'integer'],
			[['description'], 'string'],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'php:Y-m-d H:i:s']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('ListsEntry', 'Movie ID'),
			'list_id' => Yii::t('ListsEntry', 'List ID'),
			'type' => Yii::t('ListsEntry', 'Media type'),
			'themoviedb_id' => Yii::t('ListsEntry', 'Movie, Person or TV Show'),
			'description' => Yii::t('ListsEntry', 'Description (supports markdown)'),
			'position' => Yii::t('ListsEntry', 'After which element the item should be inserted'),
			'created_at' => Yii::t('ListsEntry', 'Created at'),
			'updated_at' => Yii::t('ListsEntry', 'Updated at'),
			'deleted_at' => Yii::t('ListsEntry', 'Deleted at'),
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
	 * Load the model for the TheMovieDB ID and the corresponding type.
	 *
	 * @return mixed
	 */
	protected function _loadModel()
	{
		if ($this->_model === null) {
			if (self::$_languageId === null) {
				$language = Language::find()
					->where(['iso' => Yii::$app->language])
					->one();

				if ($language === null)
					self::$_languageId = false;
				else
					self::$_languageId = $language->id;
			}

			if (self::$_languageId === false)
				return '';

			switch ($this->type) {
				case self::TYPE_MOVIE:
					$query = Movie::find();
					break;
				case self::TYPE_TV_SHOW:
					$query = Show::find();
					break;
				case self::TYPE_PERSON:
					$query = Person::find();
					break;
				default:
					throw new \yii\web\BadRequestHttpException(sprintf('Unknown type %d', $this->type));
			}

			$model = $query
				->where([
					'themoviedb_id' => $this->themoviedb_id,
					'language_id' => self::$_languageId,
				])
				->one();

			if ($model === null)
				$model = false;

			$this->_model = $model;
		}

		return $this->_model;
	}

	/**
	 * Get the title of the TheMovieDB object.
	 *
	 * @return string
	 */
	public function getTitle()
	{
		$this->_loadModel();

		if (empty($this->_model))
			return '';

		switch ($this->type) {
			case self::TYPE_MOVIE:
				return $this->_model->completeTitle;
				break;
			case self::TYPE_TV_SHOW:
				return $this->_model->completeName;
				break;
			case self::TYPE_PERSON:
				return $this->_model->name;
				break;
			default:
				throw new \yii\web\BadRequestHttpException(sprintf('Unknown type %d', $this->type));
		}
	}

	public function getImage()
	{
		$this->_loadModel();

		if (empty($this->_model))
			return '';

		//Backdrop
		//w780
		//
		//profile
		//h632

		switch ($this->type) {
			case self::TYPE_MOVIE:
				return $this->_model->backdropLargeAttribute;
				break;
			case self::TYPE_TV_SHOW:
				return $this->_model->completeName;
				break;
			case self::TYPE_PERSON:
				return $this->_model->name;
				break;
			default:
				throw new \yii\web\BadRequestHttpException(sprintf('Unknown type %d', $this->type));
		}
	}

	/**
	 * Get the link to the TheMovieDB object.
	 *
	 * @return string
	 */
	public function getPermalink()
	{
		$this->_loadModel();

		if (empty($this->_model))
			return '';

		switch ($this->type) {
			case self::TYPE_MOVIE:
				return Yii::$app->urlManager->createAbsoluteUrl(['/movie/view', 'slug' => $this->_model->slug]);
				break;
			case self::TYPE_TV_SHOW:
				return Yii::$app->urlManager->createAbsoluteUrl(['/tv/view', 'slug' => $this->_model->slug]);
				break;
			case self::TYPE_PERSON:
				return Yii::$app->urlManager->createAbsoluteUrl(['/person/view', 'id' => $this->_model->id]);
				break;
			default:
				throw new \yii\web\BadRequestHttpException(sprintf('Unknown type %d', $this->type));
		}
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getList()
	{
		return $this->hasOne(Lists::className(), ['id' => 'list_id']);
	}
}
