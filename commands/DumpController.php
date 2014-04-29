<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

class DumpController extends Controller
{
	public $defaultAction = 'export';

	public function actionExport()
	{
		$found = preg_match('/host=(.*);/', Yii::$app->db->dsn, $matches);
		if ($found !== 1)
			return "Host not found in dsn!\n";
		$host = $matches[1];

		$database = preg_match('/dbname=(.*)/', Yii::$app->db->dsn, $matches);
		if ($found !== 1)
			return "Database not found in dsn!\n";
		$database = $matches[1];

		$username = Yii::$app->db->username;
		$password = Yii::$app->db->password;
		$tablePrefix = Yii::$app->db->tablePrefix;

		$dumpfile = Yii::$app->basePath . '/tests/_data/dump.sql';

		$command = "mysqldump -d -h {$host} -u {$username} -p{$password} {$database} > {$dumpfile}";

		exec($command, $output, $return);

		if ($return > 0) {
			echo "Failed to dump table structure!\n";
			return 1;
		}

		$structure = file_get_contents($dumpfile);
		$structure = str_replace('CREATE TABLE `' . $tablePrefix, 'CREATE TABLE `', $structure);
		$structure = str_replace('DROP TABLE IF EXISTS `' . $tablePrefix, 'DROP TABLE IF EXISTS `', $structure);
		$structure = str_replace('ENGINE=InnoDB', 'ENGINE=MEMORY', $structure);

		// Replace text with varchar columns (text columns are not supported for memory engines)
		$structure = str_replace('text COLLATE', 'varchar(1024) COLLATE', $structure);

		return file_put_contents($dumpfile, $structure);
	}
}