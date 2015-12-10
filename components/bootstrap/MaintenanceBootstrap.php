<?php namespace app\components\bootstrap;

use \Yii;
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
			header('HTTP/1.1 503 Service Temporarily Unavailable');
			header('Status: 503 Service Temporarily Unavailable');
			header('Retry-After: 300');
			$app->end(0, new Response([
				'content' => Yii::t('Maintenance', 'The application is currently updating, please try again in a few seconds.')
			]));
		}
	}
}
