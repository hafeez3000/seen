<?php namespace app\components;

use \yii\base\Event;
use \yii\db\ActiveRecord;

use \app\models\UserShow;
use \app\models\UserShowRun;

class Application extends \yii\web\Application
{
	protected function bootstrap()
	{
		parent::bootstrap();

		Event::on(UserShow::className(), ActiveRecord::EVENT_AFTER_INSERT, function($event) {
			$userShow = $event->sender;

			$run = new UserShowRun;
			$run->user_id = $userShow->user_id;
			$run->show_id = $userShow->show_id;
			$run->save();
		});
	}
}