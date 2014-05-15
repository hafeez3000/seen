<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\widgets\ActiveForm;
use \yii\widgets\LinkPager;

$this->title[] = Yii::t('Movie/Dashboard', 'Your Movies');
?>

<div id="movie-dashboard">
	<div class="row" id="movie-dashboard-header">
		<div class="col-sm-6 col-md-8">
			<h1><?php echo Yii::t('Movie/Dashboard', 'Your Movies'); ?></h1>
		</div>

		<div class="col-sm-6 col-md-4">
			<?php $form = ActiveForm::begin([
				'action' => Yii::$app->urlManager->createAbsoluteUrl(['movie/load']),
			]); ?>
				<input type="hidden" id="movie-search" name="id" style="margin-top: 30px; width: 100%;">
			<?php ActiveForm::end(); ?>
		</div>
	</div>

	<?php if (count($movies)): ?>
        <h2><?php echo Yii::t('Movie/Dashboard', 'Recently Watched'); ?></h2>

		<ul id="movie-dashboard-movielist" class="list-unstyled list-inline">
			<?php foreach ($movies as $movie): ?>
				<li class="movie-dashboard-movie" id="movie-<?php echo $movie->id; ?>">
					<a href="<?php echo Url::toRoute(['view', 'slug' => $movie->slug]); ?>" title="<?php echo $movie->title; ?>">
						<img <?php echo $movie->posterUrl; ?> alt="<?php echo Html::encode($movie->title); ?>" title="<?php echo Html::encode($movie->title); ?>">
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

    <h2><?php echo Yii::t('Movie/Dashboard', 'Recommend for you'); ?></h2>
    <ul id="movie-recommend-dashboard-movielist" class="list-unstyled list-inline">
        <?php foreach ($recommendMovies as $movie): ?>
            <li class="movie-dashboard-movie" id="movie-<?php echo $movie->id; ?>">
                <a href="<?php echo Url::toRoute(['view', 'slug' => $movie->slug]); ?>" title="<?php echo $movie->title; ?>">
                    <img <?php echo $movie->posterUrl; ?> alt="<?php echo Html::encode($movie->title); ?>" title="<?php echo Html::encode($movie->title); ?>">
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

</div>
