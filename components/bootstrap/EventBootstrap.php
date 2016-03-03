<?php namespace app\components\bootstrap;

use \Yii;
use \yii\base\BootstrapInterface;
use \yii\base\Event;
use \yii\db\ActiveRecord;

use \app\models\User;
use \app\models\UserShow;
use \app\models\UserShowRun;
use \app\models\UserMovie;
use \app\models\UserEpisode;
use \app\models\UserMovieWatchlist;
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

		/**
		 * Invalidate cache tags.
		 */

		Event::on(UserEpisode::className(), ActiveRecord::EVENT_AFTER_INSERT, function($event) {
			$userEpisode = $event->sender;

			\yii\caching\TagDependency::invalidate(Yii::$app->cache, ['user-tv-' . $userEpisode->run->user_id]);
		});

		Event::on(UserMovieWatchlist::className(), ActiveRecord::EVENT_AFTER_INSERT, function($event) {
			$userMovieWatchlist = $event->sender;

			\yii\caching\TagDependency::invalidate(Yii::$app->cache, ['user-movie-watchlist-' . $userMovieWatchlist->user_id]);
		});

		Event::on(UserMovieWatchlist::className(), ActiveRecord::EVENT_AFTER_DELETE, function($event) {
			$userMovieWatchlist = $event->sender;

			\yii\caching\TagDependency::invalidate(Yii::$app->cache, ['user-movie-watchlist-' . $userMovieWatchlist->user_id]);
		});

		Event::on(UserMovie::className(), ActiveRecord::EVENT_AFTER_INSERT, function($event) {
			$userMovie = $event->sender;

			\yii\caching\TagDependency::invalidate(Yii::$app->cache, ['user-movie-seen-' . $userMovie->user_id]);
		});

		Event::on(UserMovie::className(), ActiveRecord::EVENT_AFTER_DELETE, function($event) {
			$userMovie = $event->sender;

			\yii\caching\TagDependency::invalidate(Yii::$app->cache, ['user-movie-seen-' . $userMovie->user_id]);
		});
	}
}
