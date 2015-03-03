<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \app\models\Movie;
use \app\models\Show;
use \app\models\Person;

class SitemapController extends Controller
{
	public $defaultAction = 'create';

	public function createMovieSitemap()
	{
		echo "Creating movie sitemap...\n";
		$tsStart = time();
		$currentMovieIndex = 0;

		$writer = new \XMLWriter;
		$writer->openUri(__DIR__ . '/../assets/sitemaps/movies.xml');
		$writer->startDocument('1.0', 'UTF-8');

		$writer->startElement('urlset');
		$writer->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$writer->writeAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
		foreach (Movie::find()
			->select(['id', 'themoviedb_id', 'slug', 'language_id', 'updated_at'])
			->where(['deleted_at' => null])
			->with('language')
			->asArray()
			->each(1000) as $movie)
		{
			$currentMovieIndex++;

			$alternateMovies = Movie::find()
				->select(['slug', 'language_id', 'updated_at'])
				->with('language')
				->where([
					'themoviedb_id' => $movie['themoviedb_id'],
					'deleted_at' => null
				])
				->where(['!=', 'id', $movie['id']])
				->asArray()
				->all();

			$writer->startElement('url');
			$writer->writeElement('loc', Yii::$app->urlManager->createAbsoluteUrl(['movie/view', 'slug' => $movie['slug']]));
			$writer->writeElement('lastmod', date(DATE_ATOM, strtotime($movie['updated_at'])));
			$writer->endElement(); // url

			array_push($alternateMovies, $movie);

			foreach ($alternateMovies as $alternateMovie) {
				$writer->startElement('xhtml:link');

				$writer->writeAttribute('rel', 'alternate');
				$writer->writeAttribute('hreflang', $alternateMovie['language']['iso']);
				$writer->writeAttribute('href', Yii::$app->urlManager->createAbsoluteUrl(['movie/view', 'slug' => $alternateMovie['slug']]));

				$writer->endElement(); // xhtml:link
			}

			$writer->endElement(); // url

			if ($currentMovieIndex % 1000 === 0) {
				echo "Creating sitemap for movie {$currentMovieIndex}...\n";
			}

			$writer->flush();
		}

		$writer->endElement(); //urlset
		$writer->flush();

		echo "Created movie sitemap in " . (time() - $tsStart) . " seconds.\n";
	}

	public function createShowSitemap()
	{
		echo "Creating tv sitemap...\n";
		$tsStart = time();
		$showSitemap = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml"></urlset>');
		foreach (Show::find()->where(['deleted_at' => null])->asArray()->each(1000) as $show) {
			$url = $showSitemap->addChild('url');
			$url->addChild('loc', Yii::$app->urlManager->createAbsoluteUrl(['tv/view', 'slug' => $show['slug']]));
			$url->addChild('lastmod', date(DATE_ATOM, strtotime($show['updated_at'])));
		}

		if (file_put_contents(__DIR__ . '/../assets/sitemaps/tv.xml', $showSitemap->asXML()) === false) {
			echo "Could not write tv.xml!\n";
			return false;
		}
		echo "Created tv sitemap in " . (time() - $tsStart) . " seconds.\n";
	}

	public function createPersonSitemap()
	{
		echo "Creating person sitemap...\n";
		$tsStart = time();
		$personSitemap = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml"></urlset>');
		foreach (Person::find()->where(['deleted_at' => null])->asArray()->each(1000) as $person) {
			$url = $personSitemap->addChild('url');
			$url->addChild('loc', Yii::$app->urlManager->createAbsoluteUrl(['person/view', 'id' => $person['id']]));
			$url->addChild('lastmod', date(DATE_ATOM, strtotime($person['updated_at'])));
		}
		if (file_put_contents(__DIR__ . '/../assets/sitemaps/actors.xml', $personSitemap->asXML()) === false) {
			echo "Could not write actors.xml!\n";
			return false;
		}
		echo "Created person sitemap in " . (time() - $tsStart) . " seconds.\n";
	}

	public function actionCreate()
	{
		echo "Creating Sitemaps...\n";

		$sitemapindex = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>');

		$this->createMovieSitemap();

		$sitemap = $sitemapindex->addChild('sitemap');
		$sitemap->addChild('loc', Yii::$app->urlManager->createAbsoluteUrl('/assets/sitemaps/movies.xml'));
		$sitemap->addChild('lastmod', date(DATE_ATOM));

		echo "Created movie sitemap in " . (time() - $tsStart) . " seconds.\n";

		$this->createShowSitemap();

		$sitemap = $sitemapindex->addChild('sitemap');
		$sitemap->addChild('loc', Yii::$app->urlManager->createAbsoluteUrl('/assets/sitemaps/tv.xml'));
		$sitemap->addChild('lastmod', date(DATE_ATOM));

		echo "Created tv sitemap in " . (time() - $tsStart) . " seconds.\n";

		$this->createPersonSitemap();

		echo "Writing sitemap.xml...\n";
		$sitemap = $sitemapindex->addChild('sitemap');
		$sitemap->addChild('loc', Yii::$app->urlManager->createAbsoluteUrl('/assets/sitemaps/actors.xml'));
		$sitemap->addChild('lastmod', date(DATE_ATOM));

		if (file_put_contents(__DIR__ . '/../assets/sitemaps/sitemap.xml', $sitemapindex->asXML()) === false) {
			echo "Could not write sitemap.xml!\n";
			return false;
		}

		echo "Sitemaps created.\n";
		return true;
	}
}
