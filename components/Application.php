<?php namespace app\components;

use \Yii;
use \yii\base\Event;
use \yii\db\ActiveRecord;

use \app\models\User;
use \app\models\UserShow;
use \app\models\UserShowRun;

class Application extends \yii\web\Application
{
	protected function getDefaultLanguage() {
		if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
			return $this->parseDefaultLanguage($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
		else
			return $this->parseDefaultLanguage(NULL);
		}

	protected function parseDefaultLanguage($http_accept, $deflang = "en") {
		if (isset($http_accept) && strlen($http_accept) > 1) {
			$x = explode(",",$http_accept);
			foreach ($x as $val) {

			if(preg_match("/(.*);q=([0-1]{0,1}.d{0,4})/i",$val,$matches))
				$lang[$matches[1]] = (float)$matches[2];
			else
				$lang[$val] = 1.0;
			}

			$qval = 0.0;
			foreach ($lang as $key => $value) {
				if ($value > $qval) {
					$qval = (float)$value;
					$deflang = $key;
				}
			}
		}

		if (strpos($deflang, '-') !== false)
			$deflang = substr($deflang, 0, strpos($deflang, '-'));

		return strtolower($deflang);
	}

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

		// Add google webmaster tools verification
		Event::on(\yii\base\View::className(), \yii\base\View::EVENT_BEGIN_PAGE, function($event) {
			Yii::$app->view->registerMetaTag([
				'google-site-verification' => 'BOv-OEbvo3gTTioeF7p14z3AnuANL5TMRHMLtgq_qjo',
			]);
		});

		// Set language
		$language = 'en';

		if (Yii::$app->user->isGuest) {
			if (Yii::$app->session->get('language', false) === false) {
				$language = $this->getDefaultLanguage();
				Yii::$app->session->set('language', $language);
			} else {
				$language = Yii::$app->session->get('language');
			}
		} else {
			if (isset(Yii::$app->user->identity->language->iso))
				$language = Yii::$app->user->identity->language->iso;
			else
				$language = Yii::$app->params['lang']['default'];
		}

		Yii::$app->language = $language;
	}
}