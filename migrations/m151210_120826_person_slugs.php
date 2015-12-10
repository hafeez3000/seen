<?php

use yii\db\Schema;
use yii\db\Migration;

use app\models\Person;

class m151210_120826_person_slugs extends Migration
{
	public function up()
	{
		$this->addColumn('{{%person}}', 'slug', 'varchar(255) AFTER {{name}}');

		$persons = Person::find()
			->where(['slug' => null]);

		$i = 0;
		$personCount = $persons->count();

		foreach ($persons->each(1000) as $person) {
			if ($i % 10000 === 0) {
				echo "Migrated person slug {$i}/{$personCount}\n";
			}

			$person->save();
			$i += 1000;
		}
	}

	public function down()
	{
		$this->dropColumn('{{%person}}', 'slug');
	}
}
