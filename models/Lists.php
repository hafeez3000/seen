<?php namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for table "{{%list}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property boolean $public
 * @property boolean $highlighted
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property User $user
 * @property ListEntry[] $listEntries
 */
class Lists extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%list}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'title'], 'required'],
			[['user_id'], 'integer'],
			[['public', 'highlighted'], 'boolean'],
			[['description'], 'string'],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
			[['title', 'slug'], 'string', 'max' => 255],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Lists', 'Movie ID'),
			'user_id' => Yii::t('Lists', 'User ID'),
			'title' => Yii::t('Lists', 'Title'),
			'slug' => Yii::t('Lists', 'Slug'),
			'description' => Yii::t('Lists', 'Description (supports markdown)'),
			'public' => Yii::t('Lists', 'Public'),
			'highlighted' => Yii::t('Lists', 'Highlighted'),
			'created_at' => Yii::t('Lists', 'Created at'),
			'updated_at' => Yii::t('Lists', 'Updated at'),
			'deleted_at' => Yii::t('Lists', 'Deleted at'),
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
			'slug' => [
				'class' => 'Zelenin\yii\behaviors\Slug',
				'attribute' => ['id', 'title'],
				'slugAttribute' => 'slug',
				'replacement' => '-',
				'lowercase' => true,
				'ensureUnique' => true,
				'immutable' => false,
			],
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEntries()
	{
		return $this
			->hasMany(ListsEntry::className(), ['list_id' => 'id'])
			->orderBy(['position' => SORT_DESC]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLastEntry()
	{
		return $this
			->hasOne(ListsEntry::className(), ['list_id' => 'id'])
			->orderBy(['position' => SORT_DESC])
			->limit(1);
	}
}
