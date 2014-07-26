<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title[] = Yii::t('Show/Index', 'TV Shows');
?>

<div id="tv-index">
	<div class="row" id="tv-dashboard-header">
		<div class="col-sm-6 col-md-8">
			<h1>
				<?php echo Yii::t('Show/Index', 'Popular TV Shows'); ?>

				<?php if (!Yii::$app->user->isGuest): ?>
					<small><a href="<?php echo Url::toRoute(['dashboard']); ?>"><?php echo Yii::t('Show/Dashboard', 'Your TV Shows'); ?></a> | </small>

					<small><a href="<?php echo Url::toRoute(['archive']); ?>"><?php echo Yii::t('Show/Dashboard', 'Archive'); ?></a></small>
				<?php endif; ?>
			</h1>
		</div>

		<div class="col-sm-6 col-md-4">
			<?php echo $this->render('/site/_search'); ?>
		</div>
	</div>

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
