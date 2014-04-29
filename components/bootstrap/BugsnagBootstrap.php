<?php namespace app\components\bootstrap;

use \Yii;
use \yii\base\Application;
use \yii\base\BootstrapInterface;

class BugsnagBootstrap implements BootstrapInterface
{
	/**
	 * @inheritdoc
	 */
	public function bootstrap($app)
	{
		if (!isset(Yii::$app->params['bugsnag']['key']))
			return;

		$bugsnag = new \Bugsnag_Client(Yii::$app->params['bugsnag']['key']);

		if (!isset(Yii::$app->user) || Yii::$app->user->isGuest)
			$bugsnag->setUser([
				'name' => Yii::t('Error', 'Guest'),
			]);
		else
			$bugsnag->setUser([
				'name' => Yii::$app->user->identity->name,
				'email' => Yii::$app->user->identity->email,
			]);

		$bugsnag->setReleaseStage(YII_ENV_TEST ? 'development' : 'production');
		$bugsnag->setNotifyReleaseStages(['production']);

		if (php_sapi_name() == 'CLI')
			$bugsnag->setType('console');
		else
			$bugsnag->setType('web');

		$app->set('bugsnag', $bugsnag);
	}
}