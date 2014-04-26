<?php

use \yii\db\Schema;
use \yii\db\Migration;

class m140425_172146_create_rbac_tables extends Migration
{
	public function up()
	{
		$this->createTable('{{%auth_rule}}', [
			'name' => 'varchar(64) not null',
			'data' => 'text',
			'created_at' => 'integer',
			'updated_at' => 'integer',
			'primary key ([[name]])',
		]);

		$this->createTable('{{%auth_item}}', [
			'name' => 'varchar(64) not null',
			'type' => 'integer not null',
			'description' => 'text',
			'rule_name' => 'varchar(64)',
			'data' => 'text',
			'created_at' => 'integer',
			'updated_at' => 'integer',
			'primary key ([[name]])',
			'key [[type]] ([[type]])',
		]);
		$this->addForeignKey('auth_item_rule_name', '{{%auth_item}}', 'rule_name', '{{%auth_rule}}', 'name', 'SET NULL', 'CASCADE');

		$this->createTable('{{%auth_item_child}}', [
			'parent' => 'varchar(64) not null',
			'child' => 'varchar(64) not null',
			'primary key ([[parent]], [[child]])',
		]);
		$this->addForeignKey('auth_item_child_parent', '{{%auth_item_child}}', 'parent', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');
		$this->addForeignKey('auth_item_child_child', '{{%auth_item_child}}', 'child', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');

		$this->createTable('{{%auth_assignment}}', [
			'item_name' => 'varchar(64) not null',
			'user_id' => 'varchar(64) not null',
			'created_at' => 'integer',
			'primary key ([[item_name]], [[user_id]])',
		]);
		$this->addForeignKey('auth_assignment_item_name', '{{%auth_assignment}}', 'item_name', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('{{%auth_assignment}}');
		$this->dropTable('{{%auth_item_child}}');
		$this->dropTable('{{%auth_item}}');
		$this->dropTable('{{%auth_rule}}');
	}
}
