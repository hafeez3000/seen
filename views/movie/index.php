<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title[] = Yii::t('Movie/Index', 'Movies');
?>

<div id="movie-index">
	<h1><?php echo Yii::t('Movie/Index', 'Popular Movies'); ?></h1>

	<div class="row">
		<?php foreach ($movies as $movie): ?>
			<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 movie-index">
				<a href="<?php echo Url::toRoute(['view', 'slug' => $movie->slug]); ?>" title="<?php echo Html::encode($movie->title); ?>">
					<img <?php echo $movie->posterUrlLarge; ?>>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>
