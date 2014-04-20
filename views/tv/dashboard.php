<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;
use \yii\widgets\ActiveForm;
?>

<div id="tv-dashboard">
	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-md-8">
				<h1><?php echo Yii::t('Show/Dashboard', 'Your TV Shows'); ?></h1>
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
					<a href="<?php echo Url::toRoute(['view', 'slug' => $show->slug]) ?>">
						<img src="<?php echo $show->posterUrl; ?>">

						<div class="last-seen">
							<?php if ($show->lastEpisode !== null): ?>
								<span title="<?php echo date(Yii::$app->params['lang'][Yii::$app->language]['datetime'], strtotime($show->lastEpisode->created_at)); ?>">
									<?php echo $show->lastEpisode->createdAtAgo; ?>
								</span>
							<?php else: ?>
								Last Seen: NO
							<?php endif; ?>
						</div>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
