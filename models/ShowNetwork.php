<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for Show Networks.
 *
 * @property integer $show_id
 * @property integer $network_id
 *
 * @property Network $network
 * @property Show $show
 */
class ShowNetwork extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%show_network}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['show_id', 'network_id'], 'required'],
			[['show_id', 'network_id'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'show_id' => Yii::t('Show/Network', 'Show'),
			'network_id' => Yii::t('Show/Network', 'Network'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNetwork()
	{
		return $this->hasOne(Network::className(), ['id' => 'network_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getShow()
	{
		return $this->hasOne(Show::className(), ['id' => 'show_id']);
	}
}
