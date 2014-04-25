<?php namespace app\commands;

use \Yii;

use \yii2sshconsole\Controller;

class DeployController extends Controller
{
	public function actionExec()
	{
		$this->connect(Yii::$app->params['remote']['host'], [
			'username' => Yii::$app->params['remote']['username'],
			'key' => Yii::$app->params['remote']['key'],
		]);

		$output = $this->run([
			'cd /var/www/seenapp.com/main',
			'git pull -f',
			'composer install',
			'./yii migrate',
			'grunt build_production',
		], function($line) {
			echo $line;
		});
	}
}
