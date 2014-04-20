<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;
?>

<div class="container" id="tv-dashboard">
	<h1><?php echo Yii::t('Tv/Dashboard', 'Your TV Shows'); ?></h1>

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
