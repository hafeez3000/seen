<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \app\models\User;
use \app\models\Episode;
use \app\components\Email;

class KinoxController extends Controller
{
	public function actionSync()
	{
		$users = [
			User::findByEmail('thelfensdrfer@gmail.com')
		];

		$html = file_get_html('http://www.kinox.to/index.php');

		$episodes = [];

		$table = $html->find('table', 1);
		if (!is_callable(array($table, 'find')))
			return;

		foreach ($table->find('tbody tr') as $show) {
			$element = $show->find('.Title a', 0);

			if ($element === null)
				continue;

			// Title
			$title = $element->attr['title'];

			// Language
			if (!preg_match('/\d+/', $show->find('.Icon img', 0)->attr['src'], $matches))
				continue;
			switch ($matches[0]) {
				case 1:
					$language = 'de';
					break;
				default:
					$language = 'en';
					break;
			}

			// Season and episode
			if (!preg_match('/s(\d+)e(\d+)/', $element->attr['href'], $matches))
				continue;
			$season = $matches[1];
			$episode = $matches[2];

			foreach ($users as $user) {
				$show = Yii::$app->db->createCommand('
					SELECT DISTINCT
						{{%show}}.*
					FROM
						{{%show}},
						{{%user_show}},
						{{%language}}
					WHERE
						{{%language}}.[[iso]] = :language AND
						{{%user_show}}.[[user_id]] = :user_id AND
						{{%show}}.[[id]] = {{%user_show}}.[[show_id]] AND
						{{%show}}.[[language_id]] = {{%language}}.[[id]] AND
						(
							{{%show}}.[[original_name]] LIKE :title OR
							{{%show}}.[[name]] LIKE :title
						)
					LIMIT 1
				', [
					':language' => $language,
					':user_id' => $user->id,
					':title' => $title,
				])->queryOne();

				if ($show !== false) {
					$episode = Yii::$app->db->createCommand('
						SELECT DISTINCT
							{{%episode}}.*
						FROM
							{{%episode}},
							{{%season}},
							{{%show}}
						WHERE
							{{%show}}.[[id]] = :show_id AND
							{{%season}}.[[number]] = :season AND
							{{%episode}}.[[number]] = :episode AND
							{{%episode}}.[[season_id]] = {{%season}}.[[id]] AND
							{{%season}}.[[show_id]] = {{%show}}.[[id]]
						LIMIT
							1
					', [
						':show_id' => $show['id'],
						':season' => $season,
						':episode' => $episode,
					])->queryOne();

					if ($episode !== false) {
						$userEpisode = Yii::$app->db->createCommand('
							SELECT
								{{%user_episode}}.*
							FROM
								{{%user_episode}}
							WHERE
								{{%user_episode}}.[[run_id]] = (
									SELECT
										{{user_show_run}}.[[id]]
									FROM
										{{%user_show_run}} AS {{user_show_run}}
									WHERE
										{{user_show_run}}.[[show_id]] = :show_id AND
										{{user_show_run}}.[[user_id]] = :user_id
									ORDER BY
										{{user_show_run}}.[[created_at]] DESC
									LIMIT 1
								) AND
								{{%user_episode}}.[[episode_id]] = :episode_id
							LIMIT 1
						', [
							':show_id' => $show['id'],
							':user_id' => $user->id,
							':episode_id' => $episode['id'],
						])->queryOne();

						if ($userEpisode !== false)
							continue;

						$email = new Email;
						$email->to = $user->email;
						$email->subject = Yii::t('Email/Kinox', '[SEEN/Kinox] {show} S{season}E{episode}', [
							'show' => $show['name'],
							'season' => str_pad($season, 2, '0', STR_PAD_LEFT),
							'episode' => str_pad($episode['number'], 2, '0', STR_PAD_LEFT),
						]);

						$html = Yii::t('Email/Kinox', '<h1 class="h1">New episode for {show}</h1>', [
							'show' => $show['name']
						]);
						$html .= Yii::t('Email/Kinox', '<p>View the new episode S{season}E{episode} at <a href="{url}">Kinox.to</a>.</p>', [
								'season' => str_pad($season, 2, '0', STR_PAD_LEFT),
								'episode' => str_pad($episode['number'], 2, '0', STR_PAD_LEFT),
								'url' => 'http://kinox.to' . $element->attr['href'],
							]
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
								'password-reset',
							)
						);
					}
				}
			}
		}
	}
}