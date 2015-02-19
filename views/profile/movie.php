<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\widgets\ActiveForm;

$this->title[] = Yii::t('Profile/Movie', '{name} Movies', [
	'name' => (!empty($user->name)) ? $user->name : $user->email,
]);
?>

<div id="movie-dashboard">
	<div class="row" id="movie-dashboard-header">
		<div class="col-sm-6 col-md-8">
			<h1>
				<small><a href="<?php echo Url::toRoute(['profile/index', 'profile' => $user->profile_name]); ?>"><span class="glyphicon glyphicon-arrow-left"></span></a></small>
				<?php echo Yii::t('Profile/Movie', '{name} Movies', [
					'name' => (!empty($user->name)) ? $user->name : $user->email,
				]); ?>
			</h1>
		</div>

		<div class="col-sm-6 col-md-4 search-wrapper">
			<?php echo $this->render('/site/_search'); ?>
		</div>
	</div>

	<?php if (count($watchlistMovies)): ?>
		<h2><?php echo Yii::t('Profile/Movie', 'Watchlist'); ?></h2>

		<div id="movie-watchlist-dashboard-movielist">
			<ul class="list-unstyled list-inline">
				<?php foreach ($watchlistMovies as $movie): ?>
					<li class="movie-dashboard-movie" id="movie-<?php echo $movie->id; ?>">
						<a href="<?php echo Url::toRoute(['movie/view', 'slug' => $movie->slug]); ?>" title="<?php echo $movie->completeTitle; ?>">
							<img <?php echo $movie->posterUrl; ?> alt="<?php echo Html::encode($movie->completeTitle); ?>" title="<?php echo Html::encode($movie->completeTitle); ?>">
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php else: ?>
		<div class="alert alert-info">
			<?php echo Yii::t('Profile/Movie', 'The user does not have any movies in the watchlist!'); ?>
		</div>
	<?php endif; ?>

	<?php if (count($movies)): ?>
		<h2><?php echo Yii::t('Profile/Movie', 'Recently Watched'); ?></h2>

		<div id="movie-dashboard-movielist">
			<ul class="list-unstyled list-inline">
				<?php foreach ($movies as $movie): ?>
					<li class="movie-dashboard-movie" id="movie-<?php echo $movie->id; ?>">
						<a href="<?php echo Url::toRoute(['movie/view', 'slug' => $movie->slug]); ?>" title="<?php echo $movie->completeTitle; ?>">
							<img <?php echo $movie->posterUrl; ?> alt="<?php echo Html::encode($movie->completeTitle); ?>" title="<?php echo Html::encode($movie->completeTitle); ?>">
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php else: ?>
		<div class="alert alert-info">
			<?php echo Yii::t('Profile/Movie', 'The user did not saw any movies yet!'); ?>
		</div>
	<?php endif; ?>
</div>
