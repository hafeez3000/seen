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
	<div class="row">
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
		<div id="movie-dashboard-movielist">
			<?php foreach ($movies as $movie): ?>
				<div class="movie-dashboard-movie media" id="movie-<?php echo $movie->id; ?>">
					<div class="pull-left">
						<a href="<?php echo Url::toRoute(['view', 'slug' => $movie->slug]); ?>" title="<?php echo $movie->title; ?>">
							<img <?php echo $movie->posterUrlSmall; ?> alt="<?php echo Html::encode($movie->title); ?>" title="<?php echo Html::encode($movie->title); ?>">
						</a>
					</div>

					<div class="media-body">
						<h4 class="media-heading"><a href="<?php echo Url::toRoute(['view', 'slug' => $movie->slug]); ?>"><?php echo Html::encode($movie->title); ?></a></h4>

						<?php if (!empty($movie->overview)): ?>
							<?php echo Html::encode($movie->overview); ?>
						<?php else: ?>
							<em><?php echo Yii::t('Movie/Dashboard', 'No description available!'); ?></em>
						<?php endif; ?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</div>

		<?php echo LinkPager::widget([
			'pagination' => $pages,
		]); ?>
	<?php else: ?>
		<div class="alert alert-info">
			<?php echo Yii::t('Movie/Dashboard', 'You have not watched a movie yet! Start with searching for your favorite ones'); ?>
		</div>
	<?php endif; ?>
</div>
