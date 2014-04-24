<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\widgets\ActiveForm;
?>

<div id="tv-dashboard<?php if($archive): ?>-archive<?php endif; ?>" class="tv-dashboard">
	<div class="row">
		<div class="col-sm-6 col-md-8">
			<h1>
				<?php echo $title; ?>

				<?php if (!$archive): ?>
					<small><a href="<?php echo Url::toRoute(['archive']); ?>"><?php echo Yii::t('User/Dashboard', 'Archive'); ?></a></small>
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

	<?php if (count($shows)): ?>
		<ul id="tv-dashboard-showlist" class="list-unstyled list-inline">
			<?php foreach ($shows as $show): ?>
				<li class="tv-dashboard-show" id="show-<?php echo $show->id; ?>">
					<a href="<?php echo Url::toRoute(['view', 'slug' => $show->slug]) ?>" title="<?php echo $show->name; ?>">
						<img src="<?php echo $show->posterUrl; ?>" alt="<?php echo Html::encode($show->name); ?>" title="<?php echo Html::encode($show->name); ?>">
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

						<div class="pull-right tv-dashboard-showlist-actions">
							<?php if (!$archive): ?>
								<a class="text-muted archive-show" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tv/archive-show', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show/Dashboard', 'Archive `{name}`', ['name' => $show->name]); ?>"><span class="glyphicon glyphicon-lock"></span></a>
							<?php else: ?>
								<a class="text-muted archive-show" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tv/unarchive-show', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show/Dashboard', 'Unarchive `{name}`', ['name' => $show->name]); ?>"><span class="glyphicon glyphicon-arrow-left"></span></a>
							<?php endif; ?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<div class="alert alert-info">
			<?php if (!$archive): ?>
				<?php echo Yii::t('Show/Dashboard', 'You are not subscribe to a TV Show! Start with searching for your favorite ones'); ?>
			<?php else: ?>
				<?php echo Yii::t('Show/Dashboard', 'Your archive is empty! Add TV Shows which are not in production anymore or you are currently not watching.'); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
