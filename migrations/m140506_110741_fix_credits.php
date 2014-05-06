<?php

use \yii\db\Schema;
use \yii\db\Migration;

class m140506_110741_fix_credits extends Migration
{
	public function up()
	{
		$this->truncateTable('{{%movie_cast}}');
		$this->truncateTable('{{%movie_crew}}');

		$this->truncateTable('{{%show_cast}}');
		$this->truncateTable('{{%show_crew}}');
		$this->truncateTable('{{%show_creator}}');

		Yii::$app->db->createCommand('DELETE FROM {{%person_alias}}')->execute();
		$this->truncateTable('{{%person}}');


		$this->dropColumn('{{%movie_cast}}', 'name');
		$this->dropColumn('{{%movie_cast}}', 'profile_path');
		$this->addColumn('{{%movie_cast}}', 'person_id', 'int(10) UNSIGNED COMMENT "Person" AFTER [[movie_id]]');
		$this->createIndex('person_id', '{{%movie_cast}}', 'person_id');
		$this->addForeignKey('movie_cast_person_id', '{{%movie_cast}}', 'person_id', '{{%person}}', 'id', 'CASCADE', 'CASCADE');

		$this->dropColumn('{{%movie_crew}}', 'name');
		$this->dropColumn('{{%movie_crew}}', 'profile_path');
		$this->addColumn('{{%movie_crew}}', 'person_id', 'int(10) UNSIGNED COMMENT "Person" AFTER [[movie_id]]');
		$this->createIndex('person_id', '{{%movie_crew}}', 'person_id');
		$this->addForeignKey('movie_crew_person_id', '{{%movie_crew}}', 'person_id', '{{%person}}', 'id', 'CASCADE', 'CASCADE');

		$this->dropColumn('{{%show_cast}}', 'name');
		$this->dropColumn('{{%show_cast}}', 'profile_path');
		$this->addColumn('{{%show_cast}}', 'person_id', 'int(10) UNSIGNED COMMENT "Person" AFTER [[show_id]]');
		$this->createIndex('person_id', '{{%show_cast}}', 'person_id');
		$this->addForeignKey('show_cast_person_id', '{{%show_cast}}', 'person_id', '{{%person}}', 'id', 'CASCADE', 'CASCADE');

		$this->dropColumn('{{%show_crew}}', 'name');
		$this->dropColumn('{{%show_crew}}', 'profile_path');
		$this->addColumn('{{%show_crew}}', 'person_id', 'int(10) UNSIGNED COMMENT "Person" AFTER [[show_id]]');
		$this->createIndex('person_id', '{{%show_crew}}', 'person_id');
		$this->addForeignKey('show_crew_person_id', '{{%show_crew}}', 'person_id', '{{%person}}', 'id', 'CASCADE', 'CASCADE');

		$this->alterColumn('{{%movie_cast}}', 'id', 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "ID"');
		$this->alterColumn('{{%movie_crew}}', 'id', 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "ID"');
		$this->alterColumn('{{%show_cast}}', 'id', 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "ID"');
		$this->alterColumn('{{%show_crew}}', 'id', 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "ID"');

		$this->alterColumn('{{%show_cast}}', 'credit_id', 'VARCHAR( 50 ) NULL DEFAULT NULL COMMENT "Credit ID";');
	}

	public function down()
	{
		$this->addColumn('{{%movie_cast}}', 'name', 'VARCHAR(255) DEFAULT NULL COMMENT "Name" AFTER [[movie_id]]');
		$this->addColumn('{{%movie_cast}}', 'profile_path', 'VARCHAR(255) DEFAULT NULL COMMENT "Character" AFTER [[character]]');
		$this->dropForeignKey('movie_cast_person_id', '{{%movie_cast}}');
		$this->dropIndex('person_id', '{{%movie_cast}}');
		$this->dropColumn('{{%movie_cast}}', 'person_id');

		$this->addColumn('{{%movie_crew}}', 'name', 'VARCHAR(255) DEFAULT NULL COMMENT "Name" AFTER [[movie_id]]');
		$this->addColumn('{{%movie_crew}}', 'profile_path', 'VARCHAR(255) DEFAULT NULL COMMENT "Job" AFTER [[job]]');
		$this->dropForeignKey('movie_crew_person_id', '{{%movie_crew}}');
		$this->dropIndex('person_id', '{{%movie_crew}}');
		$this->dropColumn('{{%movie_crew}}', 'person_id');

		$this->addColumn('{{%show_cast}}', 'name', 'VARCHAR(255) DEFAULT NULL COMMENT "Name" AFTER [[show_id]]');
		$this->addColumn('{{%show_cast}}', 'profile_path', 'VARCHAR(255) DEFAULT NULL COMMENT "Character" AFTER [[character]]');
		$this->dropForeignKey('show_cast_person_id', '{{%show_cast}}');
		$this->dropIndex('person_id', '{{%show_cast}}');
		$this->dropColumn('{{%show_cast}}', 'person_id');

		$this->addColumn('{{%show_crew}}', 'name', 'VARCHAR(255) DEFAULT NULL COMMENT "Name" AFTER [[show_id]]');
		$this->addColumn('{{%show_crew}}', 'profile_path', 'VARCHAR(255) DEFAULT NULL COMMENT "Job" AFTER [[job]]');
		$this->dropForeignKey('show_crew_person_id', '{{%show_crew}}');
		$this->dropIndex('person_id', '{{%show_crew}}');
		$this->dropColumn('{{%show_crew}}', 'person_id');

		$this->alterColumn('{{%movie_cast}}', 'id', 'INT(10) UNSIGNED NOT NULL COMMENT "ID"');
		$this->alterColumn('{{%movie_crew}}', 'id', 'INT(10) UNSIGNED NOT NULL COMMENT "ID"');
		$this->alterColumn('{{%show_cast}}', 'id', 'INT(10) UNSIGNED NOT NULL COMMENT "ID"');
		$this->alterColumn('{{%show_crew}}', 'id', 'INT(10) UNSIGNED NOT NULL COMMENT "ID"');

		return true;
	}
}
