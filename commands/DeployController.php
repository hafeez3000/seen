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
			'touch .maintenance',
			'git fetch --all',
			'git reset --hard origin/master',
			'composer install --optimize-autoloader --no-dev',
			'./yii migrate --interactive=0',
			'grunt build_production',
			'./yii cache/flush',
			'rm -f .maintenance',
		], function($line) {
			echo $line;
		});
	}
}
