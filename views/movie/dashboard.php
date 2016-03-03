<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title[] = Yii::t('Movie/Dashboard', 'Your Movies');
?>

<div id="movie-dashboard">
	<h1>
		<?php echo Yii::t('Movie/Dashboard', 'Your Movies'); ?>
		<small><a href="<?php echo Url::toRoute(['popular']) ?>"><?php echo Yii::t('Movie/Dashboard', 'Popular'); ?></a></small>
	</h1>

	<?php if (count($watchlistMovies)): ?>
		<h2><?php echo Yii::t('Movie/Dashboard', 'Watchlist'); ?></h2>

		<div id="movie-watchlist-dashboard-movielist">
			<ul class="list-unstyled list-inline">
				<?php foreach ($watchlistMovies as $movie): ?>
					<li class="movie-dashboard-movie" id="movie-<?php echo $movie->id; ?>">
						<a href="<?php echo Url::toRoute(['view', 'slug' => $movie->slug]); ?>" title="<?php echo $movie->completeTitle; ?>">
							<img <?php echo $movie->posterMediumAttribute; ?> alt="<?php echo Html::encode($movie->completeTitle); ?>" title="<?php echo Html::encode($movie->completeTitle); ?>">
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if (count($recommendMovies)): ?>
		<h2><?php echo Yii::t('Movie/Dashboard', 'Recommend for you'); ?></h2>
		<div id="movie-recommend-dashboard-movielist">
			<ul class="list-unstyled list-inline">
				<?php foreach ($recommendMovies as $movie): ?>
					<li class="movie-dashboard-movie" id="movie-<?php echo $movie->id; ?>">
						<a href="<?php echo Url::toRoute(['view', 'slug' => $movie->slug]); ?>" title="<?php echo $movie->completeTitle; ?>">
							<img <?php echo $movie->posterMediumAttribute; ?> alt="<?php echo Html::encode($movie->completeTitle); ?>" title="<?php echo Html::encode($movie->completeTitle); ?>">
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php else: ?>
		<div class="alert alert-info"><?php echo Yii::t('Movie/Dashboard', 'Start by adding movies you watched. After you added a few, we can recommend new movies based on your seen ones.'); ?></div>
	<?php endif; ?>

	<?php if (count($movies)): ?>
		<h2><?php echo Yii::t('Movie/Dashboard', 'Recently Watched'); ?></h2>

		<div id="movie-dashboard-movielist">
			<ul class="list-unstyled list-inline">
				<?php foreach ($movies as $movie): ?>
					<li class="movie-dashboard-movie" id="movie-<?php echo $movie->id; ?>">
						<a href="<?php echo Url::toRoute(['view', 'slug' => $movie->slug]); ?>" title="<?php echo $movie->completeTitle; ?>">
							<img <?php echo $movie->posterMediumAttribute; ?> alt="<?php echo Html::encode($movie->completeTitle); ?>" title="<?php echo Html::encode($movie->completeTitle); ?>">
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</div>
