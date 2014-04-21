<?php namespace app\components;

use \Yii;
use \yii\base\Event;
use \yii\db\ActiveRecord;

use \app\models\User;
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

		// Send welcome email
		Event::on(User::className(), User::EVENT_AFTER_REGISTER, function($event) {
			$user = $event->sender;

			$email = new Email;
			$email->to = $user->email;
			$email->subject = Yii::t('Email/Register', 'Welcome to SEEN');

			$html = Yii::t('Email/Register', '<h1 class="h1">Welcome to <span class="highlight">SEEN</span></h1>');
			$html .= Yii::t(
				'Email/Register',
				'<p>You successfully registered at <a href="{url-home}">seenapp.com</a>! Start now by subscribing to your <a href="{url-tv}">favorite tv shows</a>.</p>',
				array(
					'url-home' => Yii::$app->urlManager->createAbsoluteUrl('/'),
					'url-tv' => Yii::$app->urlManager->createAbsoluteUrl('/tv'),
				)
			);

			$email->send(
				'Default',
				array(
					array(
						'name' => 'content',
						'content' => $html,
					)
				),
				array(
					'register',
				)
			);
		});

		// Register user to mailchimp
		Event::on(User::className(), User::EVENT_AFTER_REGISTER, function($event) {
			$user = $event->sender;

			$mc = new Mailchimp();
			$mc->subscribe($user->email);
		});

		// Do not delete user show subscribptions, only set deleted timestamp
		Event::on(UserShow::className(), UserShow::EVENT_BEFORE_DELETE, function($event) {
			$event->sender->deleted_at = date('Y-m-d H:i:s');
			$event->sender->save();

			$event->isValid = false;
		});
	}
}