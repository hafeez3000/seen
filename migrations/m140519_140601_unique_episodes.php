<?php

class m140519_140601_unique_episodes extends \yii\db\Migration
{
    public function up()
    {
        $this->createIndex('show_themoviedb_id_language_id', '{{%show}}', [
            'themoviedb_id',
            'language_id',
        ], true);

        $this->createIndex('season_show_id_number', '{{%season}}', [
            'show_id',
            'number',
        ], true);

        $this->createIndex('episode_season_id_number', '{{%episode}}', [
            'season_id',
            'number',
        ], true);
    }

    public function down()
    {
        $this->dropIndex('show_themoviedb_id_language_id', '{{%show}}');
        $this->dropIndex('season_show_id_number', '{{%season}}');
        $this->dropIndex('episode_season_id_number', '{{%episode}}');
    }
}
