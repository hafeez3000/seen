<?php namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for popular TV Shows.
 *
 * @property integer $id
 * @property integer $show_id
 * @property integer $order
 * @property string $created_at
 *
 * @property Show $show
 */
class ShowPopular extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%show_popular}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['show_id', 'order'], 'required'],
			[['show_id', 'order'], 'integer'],
			[['created_at'], 'date', 'format' => 'php:Y-m-d H:i:s']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Show/Popular', 'ID'),
			'show_id' => Yii::t('Show/Popular', 'Show'),
			'order' => Yii::t('Show/Popular', 'Order'),
			'created_at' => Yii::t('Show/Popular', 'Created at'),
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
