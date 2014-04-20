<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;
use \app\components\PersonTrait;

/**
 * This is the model class for the Show Cast.
 *
 * @property integer $id
 * @property integer $show_id
 * @property integer $credit_id
 * @property string $name
 * @property string $character
 * @property string $profile_path
 * @property integer $order
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Show $show
 */
class ShowCast extends ActiveRecord
{
	use PersonTrait;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%show_cast}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'show_id'], 'required'],
			[['id', 'show_id', 'credit_id', 'order'], 'integer'],
			[['created_at', 'updated_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['name', 'character', 'profile_path'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Show/Cast', 'ID'),
			'show_id' => Yii::t('Show/Cast', 'Show ID'),
			'credit_id' => Yii::t('Show/Cast', 'Credit ID'),
			'name' => Yii::t('Show/Cast', 'Name'),
			'character' => Yii::t('Show/Cast', 'Character'),
			'profile_path' => Yii::t('Show/Cast', 'Profile path'),
			'order' => Yii::t('Show/Cast', 'Order'),
			'created_at' => Yii::t('Show/Cast', 'Created at'),
			'updated_at' => Yii::t('Show/Cast', 'Updated at'),
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
