<?php

class m140423_143443_init extends \yii\db\Migration
{
	public function up()
	{
		$this->createTable('{{%language}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'iso' => 'varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT "ISO 639-1"',
			'name' => 'varchar(100) COLLATE utf8_unicode_ci NOT NULL',
			'rtl' => 'tinyint(1) NOT NULL DEFAULT "0"',
			'en_name' => 'varchar(50) COLLATE utf8_unicode_ci NOT NULL',
			'hide' => 'tinyint(1) NOT NULL DEFAULT "0"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY ([[id]])',
		]);

		$this->createTable('{{%company}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Name"',
			'description' => 'text COLLATE utf8_unicode_ci COMMENT "Description"',
			'parent_id' => 'int(10) unsigned DEFAULT NULL COMMENT "Parent Company"',
			'headquarters' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Headquarter"',
			'homepage' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Homepage"',
			'logo_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Logo path"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY ([[id]])',
			'KEY parent_id ([[parent_id]])'
		]);

		$this->createTable('{{%country}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT "Name"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY ([[id]])',
		]);

		$this->createTable('{{%network}}', [
			'id' => 'int(10) unsigned NOT NULL COMMENT "ID"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT "Name"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY ([[id]])',
		]);

		$this->createTable('{{%person}}', [
			'id' => 'int(10) unsigned NOT NULL COMMENT "ID"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Name"',
			'biography' => 'text COLLATE utf8_unicode_ci COMMENT "Biography"',
			'birthday' => 'date DEFAULT NULL COMMENT "Birthday"',
			'deathday' => 'date DEFAULT NULL COMMENT "Deathday"',
			'homepage' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Homepage"',
			'adult' => 'tinyint(1) DEFAULT NULL COMMENT "Adult"',
			'place_of_birth' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Place of birth"',
			'profile_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Profile path"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY ([[id]])',
		]);

		$this->createTable('{{%person_alias}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'person_id' => 'int(10) unsigned NOT NULL COMMENT "Person"',
			'alias' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT "Alias"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY ([[id]])',
			'KEY person_id ([[person_id]])',
		]);
		$this->addForeignKey('person_alias_person_id', '{{%person_alias}}', 'person_id', '{{%person}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%genre}}', [
			'id' => 'int(10) unsigned NOT NULL COMMENT "ID"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT "Name"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY ([[id]])',
		]);

		$this->createTable('{{%log}}', [
			'id' => 'bigint(20) NOT NULL AUTO_INCREMENT',
			'level' => 'int(11) DEFAULT NULL',
			'category' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
			'log_time' => 'int(11) DEFAULT NULL',
			'message' => 'text COLLATE utf8_unicode_ci',
			'PRIMARY KEY ([[id]])',
			'KEY log_level (`level`)',
			'KEY log_category ([[category]])',
		]);

		$this->createTable('{{%show}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'themoviedb_id' => 'int(10) unsigned NOT NULL COMMENT "TheMovieDB"',
			'language_id' => 'int(10) unsigned NOT NULL COMMENT "Language"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Name"',
			'original_name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Original name"',
			'slug' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT "Slug"',
			'overview' => 'text COLLATE utf8_unicode_ci COMMENT "Overview"',
			'homepage' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Homepage"',
			'first_air_date' => 'date DEFAULT NULL COMMENT "First air date"',
			'last_air_date' => 'date DEFAULT NULL COMMENT "Last air date"',
			'in_production' => 'tinyint(1) DEFAULT NULL COMMENT "In production"',
			'popularity' => 'double DEFAULT NULL COMMENT "Popularity"',
			'backdrop_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Backdrop path"',
			'poster_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Poster path"',
			'status' => 'varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Staus"',
			'vote_average' => 'double DEFAULT NULL COMMENT "Average vote"',
			'vote_count' => 'int(10) unsigned DEFAULT NULL COMMENT "Vote count"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY ([[id]])',
			'KEY language_id ([[language_id]])',
			'KEY themoviedb_id ([[themoviedb_id]])',
			'KEY slug ([[slug]])',
		]);
		$this->addForeignKey('show_language_id', '{{%show}}', 'language_id', '{{%language}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%season}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'themoviedb_id' => 'int(10) unsigned NOT NULL COMMENT "TheMovieDB"',
			'show_id' => 'int(10) unsigned NOT NULL COMMENT "Show"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Name"',
			'number' => 'smallint(5) unsigned NOT NULL COMMENT "Number"',
			'overview' => 'text COLLATE utf8_unicode_ci COMMENT "Overview"',
			'poster_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Poster path"',
			'air_date' => 'date DEFAULT NULL COMMENT "Air date"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY ([[id]])',
			'KEY themoviedb_id ([[themoviedb_id]])',
			'KEY show_id ([[show_id]])',
		]);
		$this->addForeignKey('season_show_id', '{{%season}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%episode}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'themoviedb_id' => 'int(10) unsigned NOT NULL COMMENT "TheMovieDB"',
			'season_id' => 'int(10) unsigned NOT NULL COMMENT "Season"',
			'number' => 'smallint(5) unsigned DEFAULT NULL COMMENT "Number"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Name"',
			'overview' => 'text COLLATE utf8_unicode_ci COMMENT "Overview"',
			'air_date' => 'date DEFAULT NULL COMMENT "Air date"',
			'still_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Still path"',
			'vote_average' => 'double unsigned DEFAULT NULL COMMENT "Average vote"',
			'vote_count' => 'int(10) unsigned DEFAULT NULL COMMENT "Vote count"',
			'production_code' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Production code"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY ([[id]])',
			'KEY season_id ([[season_id]])',
			'KEY themoviedb_id ([[themoviedb_id]])',
		]);
		$this->addForeignKey('episode_season_id', '{{%episode}}', 'season_id', '{{%season}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%show_cast}}', [
			'id' => 'int(10) unsigned NOT NULL COMMENT "ID"',
			'show_id' => 'int(10) unsigned NOT NULL COMMENT "Show"',
			'credit_id' => 'int(10) unsigned DEFAULT NULL COMMENT "Credit ID"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Name"',
			'character' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Character"',
			'profile_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Profile path"',
			'order' => 'smallint(5) unsigned DEFAULT NULL COMMENT "Order"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'PRIMARY KEY ([[id]])',
			'KEY show_id ([[show_id]])',
		]);
		$this->addForeignKey('show_cast_show_id', '{{%show_cast}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%show_country}}', [
			'show_id' => 'int(10) unsigned NOT NULL COMMENT "Show"',
			'country_id' => 'int(10) unsigned NOT NULL COMMENT "Country"',
			'PRIMARY KEY (show_id,country_id)',
			'KEY country_id ([[country_id]])',
		]);
		$this->addForeignKey('show_country_show_id', '{{%show_country}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('show_country_country_id', '{{%show_country}}', 'country_id', '{{%country}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%show_creator}}', [
			'show_id' => 'int(10) unsigned NOT NULL COMMENT "Show"',
			'person_id' => 'int(10) unsigned NOT NULL COMMENT "Person"',
			'PRIMARY KEY (show_id,person_id)',
			'KEY person_id ([[person_id]])',
		]);
		$this->addForeignKey('show_creator_show_id', '{{%show_creator}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('show_creator_person_id', '{{%show_creator}}', 'person_id', '{{%person}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%show_crew}}', [
			'id' => 'int(11) NOT NULL COMMENT "ID"',
			'show_id' => 'int(10) unsigned NOT NULL COMMENT "Show"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Name"',
			'department' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Department"',
			'job' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Job"',
			'profile_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Profile path"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'PRIMARY KEY ([[id]])',
			'KEY show_id ([[show_id]])',
		]);
		$this->addForeignKey('show_crew_show_id', '{{%show_crew}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%show_genre}}', [
			'show_id' => 'int(10) unsigned NOT NULL COMMENT "Show"',
			'genre_id' => 'int(10) unsigned NOT NULL COMMENT "Genre"',
			'PRIMARY KEY (show_id,genre_id)',
			'KEY genre_id ([[genre_id]])',
		]);
		$this->addForeignKey('show_genre_show_id', '{{%show_genre}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('show_genre_genre_id', '{{%show_genre}}', 'genre_id', '{{%genre}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%show_network}}', [
			'show_id' => 'int(10) unsigned NOT NULL COMMENT "Show"',
			'network_id' => 'int(10) unsigned NOT NULL COMMENT "Network"',
			'PRIMARY KEY (show_id,network_id)',
			'KEY network_id ([[network_id]])',
		]);
		$this->addForeignKey('show_network_show_id', '{{%show_network}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('show_network_network_id', '{{%show_network}}', 'network_id', '{{%network}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%show_runtime}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'show_id' => 'int(11) unsigned NOT NULL COMMENT "Show"',
			'minutes' => 'smallint(5) unsigned NOT NULL COMMENT "Minutes"',
			'PRIMARY KEY ([[id]])',
			'KEY show_id ([[show_id]])',
		]);
		$this->addForeignKey('show_runtime_show_id', '{{%show_runtime}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%movie}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'themoviedb_id' => 'int(10) unsigned NOT NULL COMMENT "TheMovieDB"',
			'language_id' => 'int(10) unsigned NOT NULL',
			'title' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
			'original_title' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
			'slug' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
			'tagline' => 'text COLLATE utf8_unicode_ci',
			'overview' => 'text COLLATE utf8_unicode_ci',
			'imdb_id' => 'varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "IMDB ID"',
			'backdrop_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Backdrop path"',
			'poster_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Poster path"',
			'release_date' => 'date DEFAULT NULL COMMENT "Release date"',
			'budget' => 'int(10) unsigned DEFAULT NULL COMMENT "Budget"',
			'revenue' => 'int(10) unsigned DEFAULT NULL COMMENT "Revenue"',
			'runtime' => 'smallint(5) unsigned DEFAULT NULL COMMENT "Runtime"',
			'status' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Status"',
			'adult' => 'tinyint(1) DEFAULT NULL COMMENT "Adult"',
			'homepage' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Homepage"',
			'popularity' => 'double unsigned DEFAULT NULL COMMENT "Popularity"',
			'vote_average' => 'double unsigned DEFAULT NULL COMMENT "Average vote"',
			'vote_count' => 'int(10) unsigned DEFAULT NULL COMMENT "Vote count"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY ([[id]])',
			'KEY language_id ([[language_id]])',
			'KEY themoviedb_id ([[themoviedb_id]])',
		]);
		$this->addForeignKey('movie_language_id', '{{%movie}}', 'language_id', '{{%language}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%movie_cast}}', [
			'id' => 'int(10) unsigned NOT NULL COMMENT "ID"',
			'movie_id' => 'int(10) unsigned NOT NULL COMMENT "Movie"',
			'credit_id' => 'varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Credit ID"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Name"',
			'character' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Character"',
			'profile_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Profile path"',
			'order' => 'smallint(5) unsigned DEFAULT NULL COMMENT "Order"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'PRIMARY KEY ([[id]])',
			'KEY movie_id ([[movie_id]])',
		]);
		$this->addForeignKey('movie_cast_movie_id', '{{%movie_cast}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%movie_company}}', [
			'movie_id' => 'int(10) unsigned NOT NULL COMMENT "Movie"',
			'company_id' => 'int(10) unsigned NOT NULL COMMENT "Company"',
			'PRIMARY KEY (movie_id,company_id)',
			'KEY company_id ([[company_id]])',
		]);
		$this->addForeignKey('movie_company_movie_id', '{{%movie_company}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('movie_company_company_id', '{{%movie_company}}', 'company_id', '{{%company}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%movie_country}}', [
			'movie_id' => 'int(10) unsigned NOT NULL COMMENT "Movie"',
			'country_id' => 'int(10) unsigned NOT NULL COMMENT "Country"',
			'PRIMARY KEY (movie_id,country_id)',
			'KEY country_id ([[country_id]])',
		]);
		$this->addForeignKey('movie_country_movie_id', '{{%movie_country}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('movie_country_country_id', '{{%movie_country}}', 'country_id', '{{%country}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%movie_crew}}', [
			'id' => 'int(11) NOT NULL COMMENT "ID"',
			'movie_id' => 'int(10) unsigned NOT NULL COMMENT "Movie"',
			'credit_id' => 'varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Credit ID"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Name"',
			'department' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Department"',
			'job' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Job"',
			'profile_path' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Profile path"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'PRIMARY KEY ([[id]])',
			'KEY movie_id ([[movie_id]])',
		]);
		$this->addForeignKey('movie_crew_movie_id', '{{%movie_crew}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%movie_genre}}', [
			'movie_id' => 'int(10) unsigned NOT NULL COMMENT "Movie"',
			'genre_id' => 'int(10) unsigned NOT NULL COMMENT "Genre"',
			'PRIMARY KEY (movie_id,genre_id)',
			'KEY genre_id ([[genre_id]])',
		]);
		$this->addForeignKey('movie_genre_movie_id', '{{%movie_genre}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('movie_genre_genre_id', '{{%movie_genre}}', 'genre_id', '{{%genre}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%movie_language}}', [
			'movie_id' => 'int(10) unsigned NOT NULL COMMENT "Movie"',
			'language_id' => 'int(10) unsigned NOT NULL COMMENT "Language"',
			'PRIMARY KEY (movie_id,language_id)',
			'KEY language_id ([[language_id]])',
		]);
		$this->addForeignKey('movie_language_movie_id', '{{%movie_language}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('movie_language_language_id', '{{%movie_language}}', 'language_id', '{{%language}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%movie_similar}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'movie_id' => 'int(10) unsigned NOT NULL COMMENT "Movie"',
			'similar_to_movie_id' => 'int(10) unsigned DEFAULT NULL COMMENT "Similiar Movie"',
			'similar_to_themoviedb_id' => 'int(10) unsigned NOT NULL COMMENT "Similar to TheMovieDB"',
			'PRIMARY KEY ([[id]])',
			'KEY movie_id ([[movie_id]])',
			'KEY similar_to_movie_id ([[similar_to_movie_id]])',
		]);
		$this->addForeignKey('movie_similar_movie_id', '{{%movie_similar}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('movie_similar_similar_to_movie_id', '{{%movie_similar}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%user}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'email' => 'varchar(100) COLLATE utf8_unicode_ci NOT NULL',
			'password' => 'varchar(80) COLLATE utf8_unicode_ci NOT NULL',
			'language_id' => 'int(10) unsigned DEFAULT NULL',
			'level' => 'tinyint(3) unsigned NOT NULL DEFAULT "0"',
			'reset_key varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL',
			'validation_key varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL',
			'api_key varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL',
			'created_at' => 'datetime DEFAULT NULL',
			'updated_at' => 'datetime DEFAULT NULL',
			'deleted_at' => 'datetime DEFAULT NULL',
			'PRIMARY KEY ([[id]])',
			'KEY language_id ([[language_id]])',
		]);
		$this->addForeignKey('user_language_id', '{{%user}}', 'language_id', '{{%language}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%user_movie}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'user_id' => 'int(10) unsigned NOT NULL COMMENT "User"',
			'movie_id' => 'int(10) unsigned NOT NULL COMMENT "Movie"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'PRIMARY KEY ([[id]])',
			'KEY user_id ([[user_id]])',
			'KEY movie_id ([[movie_id]])',
		]);
		$this->addForeignKey('user_movie_user_id', '{{%user_movie}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('user_movie_movie_id', '{{%user_movie}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%user_show}}', [
			'user_id' => 'int(10) unsigned NOT NULL COMMENT "User"',
			'show_id' => 'int(10) unsigned NOT NULL COMMENT "Show"',
			'archived' => 'tinyint(1) NOT NULL DEFAULT "0" COMMENT "Archived"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY (show_id,user_id)',
			'KEY user_id ([[user_id]])',
			'KEY archived ([[archived]])',
		]);
		$this->addForeignKey('user_show_user_id', '{{%user_show}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('user_show_show_id', '{{%user_show}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%user_show_run}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'user_id' => 'int(10) unsigned NOT NULL COMMENT "User"',
			'show_id' => 'int(10) unsigned NOT NULL COMMENT "Show"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'PRIMARY KEY ([[id]])',
			'KEY user_id ([[user_id]])',
			'KEY show_id ([[show_id]])',
		]);
		$this->addForeignKey('user_show_run_user_id', '{{%user_show_run}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('user_show_run_show_id', '{{%user_show_run}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%user_episode}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'episode_id' => 'int(10) unsigned NOT NULL COMMENT "Episode"',
			'run_id' => 'int(10) unsigned NOT NULL COMMENT "Run"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'PRIMARY KEY ([[id]])',
			'KEY episode_id ([[episode_id]])',
			'KEY run_id ([[run_id]])',
		]);
		$this->addForeignKey('user_episode_episode_id', '{{%user_episode}}', 'episode_id', '{{%episode}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('user_episode_run_id', '{{%user_episode}}', 'run_id', '{{%user_show_run}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('{{%user_movie}}');
		$this->dropTable('{{%movie_cast}}');
		$this->dropTable('{{%movie_company}}');
		$this->dropTable('{{%movie_country}}');
		$this->dropTable('{{%movie_crew}}');
		$this->dropTable('{{%movie_genre}}');
		$this->dropTable('{{%movie_language}}');
		$this->dropTable('{{%movie_similar}}');
		$this->dropTable('{{%movie}}');

		$this->dropTable('{{%user_episode}}');
		$this->dropTable('{{%episode}}');
		$this->dropTable('{{%season}}');
		$this->dropTable('{{%user_show_run}}');
		$this->dropTable('{{%user_show}}');

		$this->dropTable('{{%show_cast}}');
		$this->dropTable('{{%show_country}}');
		$this->dropTable('{{%show_creator}}');
		$this->dropTable('{{%show_crew}}');
		$this->dropTable('{{%show_genre}}');
		$this->dropTable('{{%show_network}}');
		$this->dropTable('{{%show_runtime}}');
		$this->dropTable('{{%show}}');

		$this->dropTable('{{%company}}');
		$this->dropTable('{{%country}}');

		$this->dropTable('{{%genre}}');
		$this->dropTable('{{%log}}');
		$this->dropTable('{{%network}}');
		$this->dropTable('{{%person_alias}}');
		$this->dropTable('{{%person}}');
		$this->dropTable('{{%user}}');
		$this->dropTable('{{%language}}');

		return true;
	}
}
