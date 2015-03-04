<?php

use \yii\db\Migration;

class m140504_161940_language_popular extends Migration
{
	public function up()
	{
		$this->addColumn('{{%language}}', 'popular_shows_updated_at', 'DATETIME NULL COMMENT "Popular shows updated at" AFTER [[hide]]');
		$this->addColumn('{{%language}}', 'popular_movies_updated_at', 'DATETIME NULL COMMENT "Popular movies updated at" AFTER [[popular_shows_updated_at]]');
	}

	public function down()
	{
		$this->dropColumn('{{%language}}', 'popular_shows_updated_at');
		$this->dropColumn('{{%language}}', 'popular_movies_updated_at');

		return true;
	}
}
