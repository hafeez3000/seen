<?php namespace app\components\bootstrap;

use \Yii;
use \yii\base\Application;
use \yii\base\BootstrapInterface;
use \yii\base\Event;
use \yii\db\ActiveRecord;

use \PredictionIO\PredictionIOClient;

use \app\models\User;
use \app\models\Show;
use \app\models\UserShow;
use \app\models\UserShowRun;
use \app\models\Movie;
use \app\models\UserMovie;
use \app\components\Email;
use \app\components\Mailchimp;

class EventBootstrap implements BootstrapInterface
{
	/**
	 * @inheritdoc
	 */
	public function bootstrap($app)
	{
		// Create first show run for user
		Event::on(UserShow::className(), ActiveRecord::EVENT_AFTER_INSERT, function($event) {
			$userShow = $event->sender;

			$run = new UserShowRun;
			$run->user_id = $userShow->user_id;
			$run->show_id = $userShow->show_id;
			$run->save();
		});

		// Add movie to prediction.io
		Event::on(Movie::className(), ActiveRecord::EVENT_AFTER_INSERT, function($event) {
			$movie = $event->sender;

			$client = PredictionIOClient::factory([
				'appkey' => Yii::$app->params['prediction']['key'],
			]);

			$client->execute($client->getCommand('create_item', [
				'pio_iid' => 'movie-' . $movie->themoviedb_id,
				'pio_itypes' => 'movie',
				'year' => ($movie->release_date != null) ? date('Y', strtotime($movie->release_date)) : '',
				'budget' => ($movie->budget > 0) ? $movie->budget : '',
				'revenue' => ($movie->revenue > 0) ? $movie->revenue : '',
				'adult' => ($movie->adult) ? true : false,
				'votes' => ($movie->vote_average > 0) ? $movie->vote_average : '',
			]));
		});

		// Update movie at prediction.io
		Event::on(Movie::className(), ActiveRecord::EVENT_AFTER_UPDATE, function($event) {
			$movie = $event->sender;

			$client = PredictionIOClient::factory([
				'appkey' => Yii::$app->params['prediction']['key'],
			]);

			$client->execute($client->getCommand('create_item', [
				'pio_iid' => 'movie-' . $movie->themoviedb_id,
				'pio_itypes' => 'movie',
				'year' => ($movie->release_date != null) ? date('Y', strtotime($movie->release_date)) : '',
				'budget' => ($movie->budget > 0) ? $movie->budget : '',
				'revenue' => ($movie->revenue > 0) ? $movie->revenue : '',
				'adult' => ($movie->adult) ? true : false,
				'votes' => ($movie->vote_average > 0) ? $movie->vote_average : '',
			]));
		});

		// Add user movie to prediction.io
		Event::on(UserMovie::className(), ActiveRecord::EVENT_AFTER_INSERT, function($event) {
			$userMovie = $event->sender;

			$client = PredictionIOClient::factory([
				'appkey' => Yii::$app->params['prediction']['key'],
			]);

			$client->identify($userMovie->user_id);
			$client->execute($client->getCommand('record_action_on_item',  [
				'pio_action' => 'view',
				'pio_iid' => 'movie-' . $userMovie->movie->themoviedb_id,
			]));
		});

		// Add show to prediction.io
		Event::on(Show::className(), ActiveRecord::EVENT_AFTER_INSERT, function($event) {
			$show = $event->sender;

			$client = PredictionIOClient::factory([
				'appkey' => Yii::$app->params['prediction']['key'],
			]);

			$client->execute($client->getCommand('create_item', [
				'pio_iid' => 'show-' . $show->themoviedb_id,
				'pio_itypes' => 'show',
				'year' => ($show->first_air_date != null) ? date('Y', strtotime($show->first_air_date)) : '',
				'votes' => ($show->vote_average > 0) ? $show->vote_average : '',
			]));
		});

		// Update show at prediction.io
		Event::on(Show::className(), ActiveRecord::EVENT_AFTER_UPDATE, function($event) {
			$show = $event->sender;

			$client = PredictionIOClient::factory([
				'appkey' => Yii::$app->params['prediction']['key'],
			]);

			$client->execute($client->getCommand('create_item', [
				'pio_iid' => 'show-' . $show->themoviedb_id,
				'pio_itypes' => 'show',
				'year' => ($show->first_air_date != null) ? date('Y', strtotime($show->first_air_date)) : '',
				'votes' => ($show->vote_average > 0) ? $show->vote_average : '',
			]));
		});

		// Add user show to prediction.io
		Event::on(UserShow::className(), ActiveRecord::EVENT_AFTER_INSERT, function($event) {
			$userShow = $event->sender;

			$client = PredictionIOClient::factory([
				'appkey' => Yii::$app->params['prediction']['key'],
			]);

			$client->identify($userShow->user_id);
			$client->execute($client->getCommand('record_action_on_item',  [
				'pio_action' => 'view',
				'pio_iid' => 'show-' . $userShow->show->themoviedb_id,
			]));
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
				'name' => 'google-site-verification',
				'content' => 'BOv-OEbvo3gTTioeF7p14z3AnuANL5TMRHMLtgq_qjo',
			]);
		});
	}
}
