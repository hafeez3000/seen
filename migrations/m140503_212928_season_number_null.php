<?php

class m140503_212928_season_number_null extends \yii\db\Migration
{
	public function up()
	{
		$this->alterColumn('{{%season}}', 'number', 'smallint(5) unsigned DEFAULT NULL COMMENT "Number"');
	}

	public function down()
	{
		$this->alterColumn('{{%season}}', 'number', 'smallint(5) unsigned NOT NULL DEFAULT "0" COMMENT "Number"');

		return true;
	}
}
