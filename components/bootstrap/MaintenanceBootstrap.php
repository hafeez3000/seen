<?php namespace app\components\bootstrap;

use \Yii;
use \yii\base\Application;
use \yii\base\BootstrapInterface;
use \yii\web\Response;

class MaintenanceBootstrap implements BootstrapInterface
{
	/**
	 * @inheritdoc
	 */
	public function bootstrap($app)
	{
		if (file_exists($app->basePath . '/.maintenance')) {
			// Application is in maintenance mode
			//require($app->viewPath . '/maintenance.php');
			$app->end(0, new Response([
				'content' => Yii::t('Maintenance', 'The application is currently not available, please try again in a few seconds.')
			]));
		}
	}
}
