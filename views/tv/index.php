<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
?>

<div id="tv-index">
	<h1><?php echo Yii::t('Show/Index', 'Popular TV Shows'); ?></h1>

	<div class="row">
		<?php foreach ($shows as $show): ?>
			<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 tv-index">
				<a href="<?php echo Url::toRoute(['view', 'slug' => $show->slug]); ?>" title="<?php echo Html::encode($show->name); ?>">
					<img <?php echo $show->posterUrlLarge; ?> alt="<?php echo Html::encode($show->name); ?>">
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>
