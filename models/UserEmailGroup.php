<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for Relations between users and email groups.
 *
 * @property integer $user_id
 * @property integer $email_group_id
 *
 * @property EmailGroup $emailGroup
 * @property User $user
 */
class UserEmailGroup extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_email_group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'email_group_id'], 'required'],
            [['user_id', 'email_group_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('User/EmailGroup', 'User'),
            'email_group_id' => Yii::t('User/EmailGroup', 'Email group'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmailGroup()
    {
        return $this->hasOne(EmailGroup::className(), ['id' => 'email_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
