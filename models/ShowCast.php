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
 * @property integer $person_id
 * @property string $credit_id
 * @property string $character
 * @property integer $order
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Show $show
 * @property Person $person
 */
class ShowCast extends ActiveRecord
{
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
			[['show_id', 'person_id'], 'required'],
			[['id', 'show_id', 'person_id', 'order'], 'integer'],
			[['created_at', 'updated_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['credit_id'], 'string', 'max' => 50],
			[['character'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Show/Cast', 'ID'),
			'show_id' => Yii::t('Show/Cast', 'Show'),
			'person_id' => Yii::t('Show/Cast', 'Person'),
			'credit_id' => Yii::t('Show/Cast', 'Credit'),
			'character' => Yii::t('Show/Cast', 'Character'),
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

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPerson()
	{
		return $this->hasOne(Person::className(), ['id' => 'person_id']);
	}
}
