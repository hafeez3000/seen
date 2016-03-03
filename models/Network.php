<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for Networks.
 *
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Show[] $shows
 */
class Network extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%network}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name'], 'required'],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
			[['name'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Network', 'ID'),
			'name' => Yii::t('Network', 'Name'),
			'created_at' => Yii::t('Network', 'Created at'),
			'updated_at' => Yii::t('Network', 'Updated at'),
			'deleted_at' => Yii::t('Network', 'Deleted at'),
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
	public function getShows()
	{
		return $this->hasMany(Show::className(), ['id' => 'show_id'])->viaTable('prod_show_network', ['network_id' => 'id']);
	}
}
