<?php

class m140425_194032_create_email_groups extends \yii\db\Migration
{
	public function up()
	{
		$this->createTable('{{%email_group}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'name' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Name"',
			'receiver' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT "Receiver"',
			'PRIMARY KEY([[id]])',
		]);
		$this->createIndex('name', '{{%email_group}}', 'name', true);
		$this->createIndex('receiver', '{{%email_group}}', 'receiver', true);

		$this->createTable('{{%user_email_group}}', [
			'user_id' => 'int(10) unsigned NOT NULL COMMENT "User"',
			'email_group_id' => 'int(10) unsigned NOT NULL COMMENT "Email group"',
			'PRIMARY KEY ([[user_id]], [[email_group_id]])',
		]);
		$this->addForeignKey('user_email_group_user_id', '{{%user_email_group}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('user_email_group_email_group_id', '{{%user_email_group}}', 'email_group_id', '{{%email_group}}', 'id', 'CASCADE', 'CASCADE');

		$this->addColumn('{{%email}}', 'respond_user_id', 'INT UNSIGNED NULL COMMENT "Repsonded by"');
		$this->addColumn('{{%email}}', 'respond_at', 'DATETIME NULL COMMENT "Responded at"');
		$this->addColumn('{{%email}}', 'assigned_user_id', 'INT UNSIGNED NULL COMMENT "Assigned user"');

		$this->createIndex('from_email', '{{%email}}', 'from_email');
		$this->createIndex('respond_user_id', '{{%email}}', 'respond_user_id');
		$this->createIndex('assigned_user_id', '{{%email}}', 'assigned_user_id');

		$this->addForeignKey('email_respond_user_id', '{{%email}}', 'respond_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('email_assigned_user_id', '{{%email}}', 'assigned_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

		$this->createIndex('to_email', '{{%email_to}}', 'to_email');

		$this->addColumn('{{%user}}', 'name', 'VARCHAR(100) NOT NULL COMMENT "Name" AFTER [[email]]');
	}

	public function down()
	{
		$this->dropTable('{{%user_email_group}}');
		$this->dropTable('{{%email_group}}');

		$this->dropForeignKey('email_respond_user_id', '{{%email}}');
		$this->dropForeignKey('email_assigned_user_id', '{{%email}}');

		$this->dropIndex('respond_user_id', '{{%email}}');
		$this->dropIndex('assigned_user_id', '{{%email}}');
		$this->dropIndex('from_email', '{{%email}}');

		$this->dropColumn('{{%email}}', 'respond_user_id');
		$this->dropColumn('{{%email}}', 'respond_at');
		$this->dropColumn('{{%email}}', 'assigned_user_id');

		$this->dropIndex('to_email', '{{%email_to}}');

		$this->dropColumn('{{%user}}', 'name');

		return true;
	}
}
