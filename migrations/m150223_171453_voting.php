<?php

use yii\db\Schema;
use yii\db\Migration;

class m150223_171453_voting extends Migration
{
	public function up()
	{
		$this->addColumn('{{%user}}', 'themoviedb_session_id', 'VARCHAR(40) NULL AFTER [[auth_key]]');
		$this->addColumn('{{%user}}', 'themoviedb_account_id', 'VARCHAR(40) NULL AFTER [[themoviedb_session_id]]');

		$this->createTable('{{%user_movie_rating}}', [
			'id' => 'int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Movie ID"',
			'user_id' => 'int(10) UNSIGNED NOT NULL',
			'themoviedb_id' => 'int(10) UNSIGNED NOT NULL',
			'rating' => 'tinyint(3) UNSIGNED NOT NULL',
			'sync' => 'boolean NOT NULL DEFAULT 0 COMMENT "If the rating was synced with TheMovieDB"',
			'PRIMARY KEY (id)',
			'INDEX user_id ([[user_id]])',
			'INDEX themoviedb_id ([[themoviedb_id]])',
		]);
		$this->addForeignKey('user_movie_rating_user_id', '{{%user_movie_rating}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%user_show_rating}}', [
			'id' => 'int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Movie ID"',
			'user_id' => 'int(10) UNSIGNED NOT NULL',
			'themoviedb_id' => 'int(10) UNSIGNED NOT NULL',
			'rating' => 'tinyint(3) UNSIGNED NOT NULL',
			'sync' => 'boolean NOT NULL DEFAULT 0 COMMENT "If the rating was synced with TheMovieDB"',
			'PRIMARY KEY (id)',
			'INDEX user_id ([[user_id]])',
			'INDEX themoviedb_id ([[themoviedb_id]])',
		]);
		$this->addForeignKey('user_show_rating_user_id', '{{%user_show_rating}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropColumn('{{%user}}', 'themoviedb_session_id');

		$this->dropTable('{{%user_movie_rating}}');
		$this->dropTable('{{%user_show_rating}}');
	}
}
