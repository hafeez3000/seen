<?php namespace app\components\bootstrap;

use \Yii;
use \yii\base\BootstrapInterface;
use \yii\web\Response;

use \app\components\YiiMixpanel;

class MixpanelBootstrap implements BootstrapInterface
{
	/**
	 * @inheritdoc
	 */
	public function bootstrap($app)
	{
		if (Yii::$app->user->isGuest)
			YiiMixpanel::identify(\session_id());
		else
			YiiMixpanel::identify(Yii::$app->user->identity->id);
	}
}
