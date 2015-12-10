<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \app\components\MovieDb;
use \app\models\Show;
use \app\models\Movie;
use \app\models\Language;
use \app\models\Person;

/**
 * Migrate old tv shows to the new themoviedb format.
 */
class TvMigrateController extends Controller
{
	public $force = false;

	public $debug = false;

	public function options($actionId)
	{
		return [
			'force',
			'debug',
		];
	}

	protected function chooseShow($name, $shows)
	{
		$showCount = count($shows);

		if ($showCount == 1)
			return (array) $shows[0];
		$text = " - Choose result for {$name}\n\n";

		for ($i = 0; $i < $showCount; $i++) {
			$index = $i + 1;
			$text .= "{$index}: {$shows[$i]->name} ({$shows[$i]->first_air_date})\n";
		}

		$prompt = $this->prompt($text, [
			'required' => true,
			'default' => 1,
			'validator' => function($input, &$error) use($shows) {
				if ($input > $showCount || $input < 0) {
					$error = 'You have to choose a valid show';
					return false;
				}

				return true;
			}
		]);

		if ($prompt == 0)
			return false;

		echo "Taking result: " . $shows[($prompt - 1)]->name . "\n";
		return (array) $shows[($prompt - 1)];
	}

	/**
	 * Migrates tv shows.
	 */
	public function actionShows()
	{
		$userShows = Yii::$app->db->createCommand('SELECT DISTINCT {{old_prod_user_show}}.* FROM {{old_prod_user_show}}, {{prod_show}} WHERE [[migrated]] = 0')->queryAll();

		$movieDb = new MovieDb;

		$languages = Yii::$app->db->createCommand('SELECT * FROM {{%language}}')->queryAll();
		$languageCache = [];

		foreach ($languages as $language) {
			$languageCache[$language['iso']] = $language['id'];
		}

		foreach ($userShows as $oldUserShow) {
			$command = Yii::$app->db->createCommand('SELECT * FROM {{old_prod_show}} WHERE [[id]] = :id AND [[language]] = :language');
			$command->bindValue(':id', $oldUserShow['show_id']);
			$command->bindValue(':language', $oldUserShow['show_language']);

			$oldShow = $command->queryOne();

			$showAttributes = $movieDb->findTvDb($oldShow['id'], $oldShow['language'], $oldShow['name'], $oldShow['imdb_id'], date('Y', strtotime($oldShow['first_aired'])));
			if ($showAttributes === false) {
				echo "Nothing found for tv show #{$oldShow['id']}: " . $movieDb->lastError() . "\n";
				continue;
			}

			if (isset($showAttributes[0])) {
				// Found multiple results

				$showAttributes = $this->chooseShow($oldShow['name'], $showAttributes);

				if ($showAttributes === false) {
					echo "Nothing found for tv show #{$oldShow['id']}: " . $movieDb->lastError() . "\n";
					continue;
				}
			}

			if (!isset($languageCache[$oldShow['language']])) {
				$language = new Language;
				$language->iso = $oldShow['language'];
				$language->save();

				$languageCache[$language->iso] = $language->id;
			}

			$show = new Show;
			$show->attributes = $showAttributes;
			$show->id = null;
			$show->themoviedb_id = $showAttributes['id'];
			$show->language_id = $languageCache[$oldShow['language']];
			$show->tvdb_id = $oldShow['id'];
			$show->tvdb_language = $oldShow['language'];

			if (!$show->save()) {
				echo "Error saving show #{$oldShow['id']}: " . serialize($show->errors) . "\n";
				continue;
			}

			$migrateUpdateCommand = Yii::$app->db->createCommand('UPDATE {{old_prod_user_show}} SET [[migrated]] = 1 WHERE [[show_id]] = :id AND [[show_language]] = :language');
			$migrateUpdateCommand->bindValue(':id', $oldShow['id']);
			$migrateUpdateCommand->bindValue(':language', $oldShow['language']);
			$migrateUpdateCommand->execute();

			echo " - Show #{$show->id} saved.\n";
		}
	}

	public function actionUserShows()
	{
		$userShows = Yii::$app->db->createCommand('SELECT {{old_prod_user_show}}.* FROM {{old_prod_user_show}} WHERE [[migrated_user]] = 0')->queryAll();

		foreach ($userShows as $userShowOld) {
			$showCommand = Yii::$app->db->createCommand('SELECT * FROM {{%show}} WHERE [[tvdb_id]] = :id AND [[tvdb_language]] = :language');
			$showCommand->bindValue(':id', $userShowOld['show_id']);
			$showCommand->bindValue(':language', $userShowOld['show_language']);
			$show = $showCommand->queryOne();

			if ($show) {
				$insertCommand = Yii::$app->db->createCommand('INSERT INTO {{%user_show}}([[user_id]], [[show_id]], [[created_at]]) VALUES(:user_id, :show_id, :created_at)');
				$insertCommand->bindValue(':user_id', $userShowOld['user_id']);
				$insertCommand->bindValue(':show_id', $show['id']);
				$insertCommand->bindValue(':created_at', date('Y-m-d H:i:s'));

				if (!$insertCommand->execute()) {
					echo "Could not insert user show: {$userShowOld->id}\n";
					continue;
				}

				$migrateUpdateCommand = Yii::$app->db->createCommand('UPDATE {{old_prod_user_show}} SET [[migrated_user]] = 1 WHERE [[show_id]] = :id AND [[show_language]] = :language');
				$migrateUpdateCommand->bindValue(':id', $userShowOld['show_id']);
				$migrateUpdateCommand->bindValue(':language', $userShowOld['show_language']);
				$migrateUpdateCommand->execute();
			} else {
				echo "Could not find show: {$userShowOld->id}\n";
				continue;
			}
		}
	}

	public function actionUserRuns()
	{
		$userRuns = Yii::$app->db->createCommand('SELECT * FROM {{old_prod_show_run}} WHERE [[migrated]] = 0')->queryAll();

		foreach ($userRuns as $oldRun) {
			$showCommand = Yii::$app->db->createCommand('SELECT * FROM {{%show}} WHERE [[tvdb_id]] = :id AND [[tvdb_language]] = :language');
			$showCommand->bindValue(':id', $oldRun['show_id']);
			$showCommand->bindValue(':language', $oldRun['show_language']);
			$show = $showCommand->queryOne();

			if ($show) {
				$insertCommand = Yii::$app->db->createCommand('INSERT INTO {{%user_show_run}}([[user_id]], [[show_id]], [[created_at]], [[tvdb_id]]) VALUES(:user_id, :show_id, :created_at, :id)');
				$insertCommand->bindValue(':user_id', $oldRun['user_id']);
				$insertCommand->bindValue(':show_id', $show['id']);
				$insertCommand->bindValue(':created_at', $oldRun['created']);
				$insertCommand->bindValue(':id', $oldRun['id']);

				if (!$insertCommand->execute()) {
					echo "Could not inser user show run: {$oldRun->id}\n";
					continue;
				}

				$migrateUpdateCommand = Yii::$app->db->createCommand('UPDATE {{old_prod_show_run}} SET [[migrated]] = 1 WHERE [[show_id]] = :id AND [[show_language]] = :language');
				$migrateUpdateCommand->bindValue(':id', $oldRun['show_id']);
				$migrateUpdateCommand->bindValue(':language', $oldRun['show_language']);
				$migrateUpdateCommand->execute();
			} else {
				echo "Could not find show: {$oldRun['show_id']} {$oldRun['show_language']}\n";
				continue;
			}
		}
	}

	public function actionEpisodes()
	{
		$userEpisodes = Yii::$app->db->createCommand('SELECT
				{{old_prod_user_episode}}.[[user_id]],
				{{old_prod_user_episode}}.[[created]] AS [[created_at]],
				{{%episode}}.[[id]] AS [[episode_id]],
				{{%user_show_run}}.[[id]] AS [[run_id]]
			FROM
				{{old_prod_user_episode}},
				{{old_prod_episode}},
				{{%user_show_run}},
				{{%episode}}
			WHERE
				{{old_prod_user_episode}}.[[migrated]] = 0 AND
				{{%user_show_run}}.[[tvdb_id]] = {{old_prod_user_episode}}.[[show_run_id]] AND
				{{old_prod_episode}}.[[id]] = {{old_prod_user_episode}}.[[episode_id]] AND
				{{old_prod_episode}}.[[language]] = {{old_prod_user_episode}}.[[episode_language]] AND
				{{%episode}}.[[id]] = (
					SELECT
						{{episode}}.[[id]]
					FROM
						{{%episode}} AS {{episode}}
					WHERE
						{{episode}}.[[number]] = {{old_prod_episode}}.[[episode]] AND
						{{episode}}.[[season_id]] = (
							SELECT
								{{season}}.[[id]]
							FROM
								{{%season}} AS {{season}},
								{{%show}} AS {{show}}
							WHERE
								{{season}}.[[number]] = {{old_prod_episode}}.[[season]] AND
								{{season}}.[[show_id]] = {{show}}.id AND
								{{show}}.[[tvdb_id]] = {{old_prod_episode}}.[[show_id]] AND
								{{show}}.[[tvdb_language]] = {{old_prod_episode}}.[[show_language]]
						)
				)
			')->queryAll();

		foreach ($userEpisodes as $episode) {
			$insertCommand = Yii::$app->db->createCommand('INSERT INTO {{%user_episode}}([[user_id]], [[episode_id]], [[run_id]], [[created_at]]) VALUES(:user_id, :episode_id, :run_id, :created_at)');
			$insertCommand->bindValue(':user_id', $episode['user_id']);
			$insertCommand->bindValue(':episode_id', $episode['episode_id']);
			$insertCommand->bindValue(':run_id', $episode['run_id']);
			$insertCommand->bindValue(':created_at', $episode['created_at']);

			if (!$insertCommand->execute()) {
				echo "Could not inser user episode: {$episode->id}\n";
				continue;
			}
		}
	}

	/**
	 * Try to fix incorrect slugs
	 */
	public function actionFixSlugs()
	{
		$shows = Show::find();

		if (!$this->force)
			$shows = $shows
				->where(['like', 'slug', 'slug'])
				->orWhere('[[slug]] REGEXP "\\-[0-9]+$"')
				->orWhere(['slug' => '']);

		if ($this->debug)
			echo "Fixing {$shows->count()} show slugs...\n";

		foreach ($shows->each() as $show) {
			$show->slug = '';
			$show->save();
		}

		$movies = Movie::find();

		if (!$this->force)
			$movies = $movies
				->where(['like', 'slug', 'slug'])
				->orWhere('[[slug]] REGEXP "\\-[0-9]+$"')
				->orWhere(['slug' => '']);

		if ($this->debug)
			echo "Fixing {$movies->count()} movie slugs...\n";

		foreach ($movies->each() as $movie) {
			$movie->slug = '';
			$movie->save();
		}
	}

	/**
	 * Delete duplicate shows and movies
	 */
	public function actionDeleteDuplicates()
	{
		$shows = Yii::$app->db->createCommand('
			DELETE
				{{n1}}
			FROM
				{{%show}} {{n1}},
				{{%show}} {{n2}}
			WHERE
				{{n1}}.[[id]] < {{n2}}.[[id]] AND
				{{n1}}.[[themoviedb_id]] = {{n2}}.[[themoviedb_id]] AND
				{{n1}}.[[language_id]] = {{n2}}.[[language_id]]'
			)->execute();
		$movies = Yii::$app->db->createCommand('
			DELETE
				{{n1}}
			FROM
				{{%movie}} {{n1}},
				{{%movie}} {{n2}}
			WHERE
				{{n1}}.[[id]] < {{n2}}.[[id]] AND
				{{n1}}.[[themoviedb_id]] = {{n2}}.[[themoviedb_id]] AND
				{{n1}}.[[language_id]] = {{n2}}.[[language_id]]'
			)->execute();

		echo "Deleted {$shows} shows and {$movies} movies\n";
	}

	/**
	 * Add slugs to persons.
	 *
	 * @return void
	 */
	public function actionAddPersonSlugs()
	{
		$persons = Person::find()
			->where(['slug' => null]);

		$i = 0;
		$personCount = $persons->count();

		foreach ($persons->each(1000) as $person) {
			if ($i % 10000 === 0) {
				echo "Migrated person slug {$i}/{$personCount}\n";
			}

			$person->save();
			$i++;
		}
	}
}
