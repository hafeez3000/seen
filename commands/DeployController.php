<?php namespace app\commands;

use \Yii;

use \yii2sshconsole\Controller;

class DeployController extends Controller
{
	public $defaultAction = 'exec';

	public function actionExec()
	{
		$this->connect(Yii::$app->params['remote']['host'], [
			'username' => Yii::$app->params['remote']['username'],
			'key' => Yii::$app->params['remote']['key'],
		]);

		$output = $this->run([
			'cd /var/www/seenapp.com/main',
			'git fetch -all',
			'git reset --hard origin/master',
			'composer install',
			'./yii migrate --interactive=0',
			'grunt build_production',
			'./yii cache/flush'
		], function($line) {
			echo $line;
		});
	}
}
