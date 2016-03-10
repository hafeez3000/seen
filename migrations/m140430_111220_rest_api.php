<?php

use \yii\db\Migration;

class m140430_111220_rest_api extends Migration
{
	public function up()
	{
		// Developers create oauth applications (consumer)
		$this->createTable('{{%oauth_application}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'user_id' => 'int(10) unsigned NOT NULL COMMENT "User"',
			'name' => 'varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Name"',
			'description' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Description"',
			'website' => 'varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Website"',
			'key' => 'varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Key"',
			'secret' => 'varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Secret"',
			'callback' => 'varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Callback url"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'PRIMARY KEY([[id]])',
			'KEY [[user_id]] ([[user_id]])',
		]);
		$this->addForeignKey('oauth_application_user_id', '{{%oauth_application}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

		// Create request token on user authorization
		$this->createTable('{{%oauth_request_token}}', [
			'request_token' => 'varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT "Request token"',
			'user_id' => 'int(10) unsigned NOT NULL COMMENT "User"',
			'oauth_application_id' => 'int(10) unsigned NOT NULL COMMENT "Oauth application"',
			'scopes' => 'text COLLATE utf8_unicode_ci COMMENT "Scopes"',
			'expires_at' => 'datetime DEFAULT NULL COMMENT "Expires at"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'PRIMARY KEY([[request_token]])',
			'KEY [[user_id]] ([[user_id]])',
			'KEY [[oauth_application_id]] ([[oauth_application_id]])',
		]);
		$this->addForeignKey('oauth_request_token_user_id', '{{%oauth_request_token}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('oauth_request_token_oauth_application_id', '{{%oauth_request_token}}', 'oauth_application_id', '{{%oauth_application}}', 'id', 'CASCADE', 'CASCADE');

		// Access tokens
		$this->createTable('{{%oauth_access_token}}', [
			'access_token' => 'varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT "Access token"',
			'user_id' => 'int(10) unsigned NOT NULL COMMENT "User"',
			'oauth_application_id' => 'int(10) unsigned NOT NULL COMMENT "Oauth application"',
			'scopes' => 'text COLLATE utf8_unicode_ci COMMENT "Scopes"',
			'expires_at' => 'datetime DEFAULT NULL COMMENT "Expires at"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'PRIMARY KEY([[access_token]])',
			'KEY [[user_id]] ([[user_id]])',
			'KEY [[oauth_application_id]] ([[oauth_application_id]])',
		]);
		$this->addForeignKey('oauth_access_token_user_id', '{{%oauth_access_token}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('oauth_access_token_oauth_application_id', '{{%oauth_access_token}}', 'oauth_application_id', '{{%oauth_application}}', 'id', 'CASCADE', 'CASCADE');

		// Refresh tokens
		$this->createTable('{{%oauth_refresh_token}}', [
			'refresh_token' => 'varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT "Refresh token"',
			'user_id' => 'int(10) unsigned NOT NULL COMMENT "User"',
			'oauth_application_id' => 'int(10) unsigned NOT NULL COMMENT "Oauth application"',
			'scopes' => 'text COLLATE utf8_unicode_ci COMMENT "Scopes"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'PRIMARY KEY([[refresh_token]])',
			'KEY [[user_id]] ([[user_id]])',
			'KEY [[oauth_application_id]] ([[oauth_application_id]])',
		]);
		$this->addForeignKey('oauth_refresh_token_user_id', '{{%oauth_refresh_token}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('oauth_refresh_token_oauth_application_id', '{{%oauth_refresh_token}}', 'oauth_application_id', '{{%oauth_application}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('{{%oauth_refresh_token}}');
		$this->dropTable('{{%oauth_access_token}}');
		$this->dropTable('{{%oauth_request_token}}');
		$this->dropTable('{{%oauth_application}}');
	}
}
