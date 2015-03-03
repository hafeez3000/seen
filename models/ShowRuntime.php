<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for Show Runtimes.
 *
 * @property integer $id
 * @property integer $show_id
 * @property integer $minutes
 *
 * @property Show $show
 */
class ShowRuntime extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%show_runtime}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['show_id', 'minutes'], 'required'],
            [['show_id', 'minutes'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('Show/Runtime', 'ID'),
            'show_id' => Yii::t('Show/Runtime', 'Show'),
            'minutes' => Yii::t('Show/Runtime', 'Minutes'),
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
