<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;
use \yii\widgets\ActiveForm;
?>

<div id="tv-dashboard<?php if($archive): ?>-archive<?php endif; ?>" class="tv-dashboard">
	<div class="row" id="tv-dashboard-header">
		<div class="col-sm-6 col-md-8">
			<h1>
				<?php echo $title; ?>

				<?php if (!$archive): ?>
					<small><a href="<?php echo Url::toRoute(['archive']); ?>"><?php echo Yii::t('Show/Dashboard', 'Archive'); ?></a> | </small>
				<?php else: ?>
					<small><a href="<?php echo Url::toRoute(['dashboard']); ?>"><?php echo Yii::t('Show/Dashboard', 'Your TV Shows'); ?></a> | </small>
				<?php endif; ?>

				<small><a href="<?php echo Url::toRoute(['popular']); ?>"><?php echo Yii::t('Show/Dashboard', 'Popular'); ?></a></small>
			</h1>
		</div>

		<div class="col-sm-6 col-md-4">
			<?php echo $this->render('/site/_search'); ?>
		</div>
	</div>

	<?php if (count($shows)): ?>
		<ul id="tv-dashboard-showlist" class="list-unstyled list-inline">
			<?php foreach ($shows as $show): ?>
				<?php echo $this->render('_view', [
					'show' => $show,
					'archive' => $archive,
				]); ?>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<div class="alert alert-info">
			<?php if (!$archive): ?>
				<?php echo Yii::t('Show/Dashboard', 'You are not subscribe to a TV Show! Start with searching for your favorites.'); ?>
			<?php else: ?>
				<?php echo Yii::t('Show/Dashboard', 'Your archive is empty! Add TV Shows which are not in production anymore or you are currently not watching.'); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
