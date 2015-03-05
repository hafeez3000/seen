<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title[] = $movie->completeTitle;
$this->title[] = Yii::t('Movie/View', 'Movies');
?>

<div id="movie-view">
	<?php if ($movieNative !== null): ?>
		<div class="alert alert-info">
			<?php echo Yii::t('Movie', 'The Movie is also available in <a href="{url}" title="{title}">{language}</a>', [
				'url' => Url::toRoute(['view', 'slug' => $movieNative->slug]),
				'title' => $movieNative->title,
				'language' => $movieNative->language->name,
			]); ?>
		</div>
	<?php endif; ?>

	<h1><?php echo Html::encode($movie->completeTitle); ?></h1>

	<div class="row">
		<div class="col-sm-4">
			<img <?php echo $movie->posterUrlLarge; ?> alt="<?php echo Html::encode($movie->completeTitle); ?>" title="<?php echo Html::encode($movie->completeTitle); ?>">
		</div>

		<div class="col-sm-8">
			<div class="row">
				<div class="col-md-7" id="movie-view-overview">
					<?php if (!empty($movie->overview)): ?>
						<?php echo Html::encode($movie->overview); ?>
					<?php else: ?>
						<div class="alert alert-info">
							<?php echo Yii::t('Movie/View', 'The movie currently does not have a description. Help by <a href="{url}">adding one</a>!', [
								'url' => 'https://www.themoviedb.org/movie/' . $movie->themoviedb_id . '/edit?' . http_build_query(['language' => $movie->language->iso]),
							]) ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="col-md-5 movie-view-details" id="movie-view-details-side">
					<?php if (count($userMovies) === 0): ?>
						<a href="<?php echo Url::toRoute(['watch', 'slug' => $movie->slug]); ?>" class="btn btn-block btn-sm btn-primary">
							<?php echo Yii::t('Movie/View', 'Watched'); ?>
						</a>
					<?php else: ?>
						<a href="<?php echo Url::toRoute(['watch', 'slug' => $movie->slug]); ?>" class="btn btn-block btn-sm btn-default">
							<?php echo Yii::t('Movie/View', 'Watched again'); ?>
						</a>
					<?php endif; ?>

					<br>

					<?php if ($movie->onWatchlist): ?>
						<a href="<?php echo Url::toRoute(['watchlist/remove', 'slug' => $movie->slug]); ?>" class="btn btn-block btn-sm btn-default">
							<?php echo Yii::t('Movie/View', 'Remove from watchlist'); ?>
						</a>
					<?php else: ?>
						<a href="<?php echo Url::toRoute(['watchlist/add', 'slug' => $movie->slug]); ?>" class="btn btn-block btn-sm btn-success">
							<?php echo Yii::t('Movie/View', 'Add to watchlist'); ?>
						</a>
					<?php endif; ?>

					<br>

					<?php echo $this->render('_details', [
						'movie' => $movie,
						'userMovies' => $userMovies,
						'userRating' => $userRating,
					]); ?>
				</div>
			</div>

			<?php if (count($movie->videos)): ?>
				<div id="movie-view-videos">
					<h2><?php echo Yii::t('Movie/View', 'Videos'); ?></h2>

					<?php foreach ($movie->videos as $video): if ($video->site == 'YouTube'): ?>
						<div class="embed-responsive embed-responsive-16by9">
							<iframe src="//www.youtube.com/embed/<?php echo $video->key; ?>" allowfullscreen></iframe>
						</div>
					<?php endif; endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if (count($movie->cast)): ?>
				<div id="movie-view-cast-wrapper">
					<h2><?php echo Yii::t('Movie/View', 'Cast'); ?></h2>

					<ul id="movie-view-cast" class="list-unstyled list-inline">
						<?php foreach ($movie->cast as $cast): ?>
							<li>
								<a href="<?php echo Url::toRoute(['/person/view', 'id' => $cast->person->id]); ?>" title="<?php echo (!empty($cast->character)) ? Html::encode(Yii::t('Movie',
									'{name} as {character}',
									[
										'name' => $cast->person->name,
										'character' => $cast->character,
									]
								)) : Html::encode($cast->person->name); ?>">
									<img <?php echo $cast->person->profileUrl; ?> alt="<?php echo Html::encode(Yii::t('Movie', '{name} as {character}', [
										'name' => $cast->person->name,
										'character' => $cast->character,
									])); ?>">
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if (count($movie->crew)): ?>
				<div id="movie-view-crew-wrapper">
					<h2><?php echo Yii::t('Movie/View', 'Crew'); ?></h2>

					<ul id="movie-view-crew" class="list-unstyled list-inline">
						<?php foreach ($movie->crew as $crew): ?>
							<li>
								<a href="<?php echo Url::toRoute(['/person/view', 'id' => $crew->person->id]); ?>" title="<?php echo Html::encode(Yii::t('Movie', '{job}: {name}', [
									'name' => $crew->person->name,
									'job' => $crew->job,
								])); ?>">
									<img <?php echo $crew->person->profileUrl; ?> alt="<?php echo Html::encode(Yii::t('Movie', '{job}: {name}', [
										'name' => $crew->person->name,
										'job' => $crew->job,
									])); ?>">
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if (count($movie->similarMovies)): ?>
				<div id="movie-view-similar-wrapper">
					<h2><?php echo Yii::t('Movie/View', 'Similar Movies'); ?></h2>

					<ul id="movie-view-similar" class="list-unstyled list-inline">
						<?php foreach ($movie->similarMovies as $similarMovie): ?>
							<?php if ($similarMovie->id != $movie->id): ?>
								<li class="<?php if (count($similarMovie->userWatches) > 0): ?>movie-view-similar-watched<?php endif; ?>">
									<a href="<?php echo Url::toRoute(['view', 'slug' => $similarMovie->slug]); ?>" title="<?php echo Html::encode($similarMovie->title); ?>">
										<img <?php echo $similarMovie->posterUrlSmall; ?> alt="<?php echo Html::encode($similarMovie->title); ?>">
									</a>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
