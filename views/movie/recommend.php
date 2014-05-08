<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title[] = Yii::t('Movie/Recommend', 'Recommend Movies');
?>

<div id="movie-recommend">
	<h1><?php echo Yii::t('Movie/Recommend', 'Recommend Movies'); ?> <small><a href="<?php echo Url::toRoute(['movie/index']) ?>"><?php echo Yii::t('Movie/Recommend', 'Your movies') ?></a></small></h1>

	<div class="row">
		<?php foreach ($movies as $movie): ?>
			<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 movie-recommend">
				<a href="<?php echo Url::toRoute(['view', 'slug' => $movie->slug]); ?>" title="<?php echo Html::encode($movie->title); ?>">
					<img <?php echo $movie->posterUrlLarge; ?>>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>
