<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;
use \yii\widgets\ActiveForm;
?>

<div id="tv-dashboard<?php if($archive): ?>-archive<?php endif; ?>" class="tv-dashboard">
	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-md-8">
				<h1>
					<?php echo $title; ?>

					<?php if (!$archive): ?>
						<small><a href="<?php echo Url::toRoute(['archive']); ?>" title="<?php echo Yii::t('User/Dashboard', 'Archive'); ?>"><span class="glyphicon glyphicon-lock"></span></a></small>
					<?php endif; ?>
				</h1>
			</div>

			<div class="col-sm-6 col-md-4">
				<?php $form = ActiveForm::begin([
					'action' => Yii::$app->urlManager->createAbsoluteUrl(['tv/load']),
				]); ?>
					<input type="hidden" id="tv-search" name="id" style="margin-top: 30px; width: 100%;">
				<?php ActiveForm::end(); ?>
			</div>
		</div>

		<ul id="tv-dashboard-showlist" class="list-unstyled list-inline">
			<?php foreach ($shows as $show): ?>
				<li id="show-<?php echo $show->id; ?>">
					<a href="<?php echo Url::toRoute(['view', 'slug' => $show->slug]) ?>" title="<?php echo $show->name; ?>">
						<img src="<?php echo $show->posterUrl; ?>">
					</a>

					<div class="last-seen clearfix">
						<div class="pull-left">
							<?php if ($show->lastEpisode !== null): ?>
								<span title="<?php echo date(Yii::$app->params['lang'][Yii::$app->language]['datetime'], strtotime($show->lastEpisode->created_at)); ?>">
									<?php echo $show->lastEpisode->createdAtAgo; ?>
								</span>
							<?php else: ?>
								<?php echo Yii::t('Show/Dashboard', 'Not seen yet'); ?>
							<?php endif; ?>
						</div>

						<div class="pull-right">
							<?php if (!$archive): ?>
								<a href="<?php echo Url::toRoute(['tv/archive-show', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show/Dashboard', 'Archive `{name}`', ['name' => $show->name]); ?>"><span class="glyphicon glyphicon-lock"></span></a>
							<?php else: ?>
								<a href="<?php echo Url::toRoute(['tv/unarchive-show', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show/Dashboard', 'Unarchive `{name}`', ['name' => $show->name]); ?>"><span class="glyphicon glyphicon-arrow-left"></span></a>
							<?php endif; ?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
