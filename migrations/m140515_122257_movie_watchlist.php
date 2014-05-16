<?php

use \yii\db\Schema;
use \yii\db\Migration;

class m140515_122257_movie_watchlist extends Migration
{
	public function up()
	{
		$this->createTable('{{%user_movie_watchlist}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'movie_id' => 'int(10) unsigned NOT NULL COMMENT "Movie"',
			'user_id' => 'int(10) unsigned NOT NULL COMMENT "User"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'PRIMARY KEY ([[id]])',
			'KEY movie_id ([[movie_id]])',
			'KEY user_id ([[user_id]])',
		]);

		$this->addForeignKey('user_movie_watchlist_movie_id', '{{%user_movie_watchlist}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('user_movie_watchlist_user_id', '{{%user_movie_watchlist}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('{{%user_movie_watchlist}}');

		return true;
	}
}
