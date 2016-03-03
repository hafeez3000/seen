<?php

namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for User Movie watchlists.
 *
 * @property integer $id
 * @property integer $movie_id
 * @property integer $user_id
 * @property string $created_at
 *
 * @property User $user
 * @property Movie $movie
 */
class UserMovieWatchlist extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_movie_watchlist}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['movie_id', 'user_id'], 'required'],
            [['movie_id', 'user_id'], 'integer'],
            [['created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('User/MovieWatchlist', 'ID'),
            'movie_id' => Yii::t('User/MovieWatchlist', 'Movie'),
            'user_id' => Yii::t('User/MovieWatchlist', 'User'),
            'created_at' => Yii::t('User/MovieWatchlist', 'Created at'),
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
                ],
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
    public function getMovie()
    {
        return $this->hasOne(Movie::className(), ['id' => 'movie_id']);
    }
}
