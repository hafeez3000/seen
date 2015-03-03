<?php

use \yii\db\Migration;

class m140425_121149_create_email_table extends Migration
{
	public function up()
	{
		$this->createTable('{{%email}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'ts' => 'timestamp NULL DEFAULT NULL COMMENT "Timestamp"',
			'event' => 'varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Event"',
			'text' => 'text COLLATE utf8_unicode_ci COMMENT "Text"',
			'html' => 'text COLLATE utf8_unicode_ci COMMENT "Html"',
			'from_email' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "From (Email)"',
			'from_name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "From (Name)"',
			'subject' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Subject"',
			'spam_score' => 'float NOT NULL DEFAULT "0" COMMENT "Spam score"',
			'PRIMARY KEY ([[id]])',
			'KEY [[ts]] ([[ts]])',
		]);

		$this->createTable('{{%email_attachment}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'email_id' => 'int(10) unsigned NOT NULL COMMENT "Email"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT "Name"',
			'type' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT "Type"',
			'PRIMARY KEY ([[id]])',
			'KEY [[email_id]] ([[email_id]])',
		]);
		$this->addForeignKey('email_attachment_email_id', '{{%email_attachment}}', 'email_id', '{{%email}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%email_to}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'email_id' => 'int(10) unsigned NOT NULL COMMENT "Email"',
			'to_email' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT "Email"',
			'to_name' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT "Name"',
			'PRIMARY KEY ([[id]])',
			'KEY [[email_id]] ([[email_id]])',
		]);
		$this->addForeignKey('email_to_email_id', '{{%email_to}}', 'email_id', '{{%email}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('{{%email_to}}');
		$this->dropTable('{{%email_attachment}}');
		$this->dropTable('{{%email}}');

		return true;
	}
}
