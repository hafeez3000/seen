<?php

class m140915_161052_movie_shows_videos extends \yii\db\Migration
{
	public function up()
	{
		$this->createTable('{{%movie_video}}', [
			'id' => 'varchar(32) NOT NULL COMMENT "The Movie Database ID"',
			'movie_id' => 'int(10) UNSIGNED NOT NULL COMMENT "Movie"',
			'key' => 'varchar(255)',
			'name' => 'varchar(255)',
			'site' => 'varchar(127)',
			'size' => 'int(10) UNSIGNED',
			'type' => 'varchar(31)',
			'PRIMARY KEY (id)',
			'KEY movie_id ([[movie_id]])',
		]);
		$this->addForeignKey('movie_video_movie_id', '{{%movie_video}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%show_video}}', [
			'id' => 'varchar(32) NOT NULL COMMENT "The Movie Database ID"',
			'show_id' => 'int(10) UNSIGNED NOT NULL COMMENT "Show"',
			'key' => 'varchar(255)',
			'name' => 'varchar(255)',
			'site' => 'varchar(127)',
			'size' => 'int(10) UNSIGNED',
			'type' => 'varchar(31)',
			'PRIMARY KEY (id)',
			'KEY show_id ([[show_id]])',
		]);
		$this->addForeignKey('show_video_show_id', '{{%show_video}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('{{%movie_video}}');
		$this->dropTable('{{%show_video}}');
	}
}
