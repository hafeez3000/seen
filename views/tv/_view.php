<?php
/**
* @var yii\web\View $this
*/

use \yii\helpers\Html;
use \yii\helpers\Url;

use \app\components\LanguageHelper;
?>

<li class="tv-dashboard-show" id="show-<?php echo $show->id; ?>" data-id="<?php echo $show->id; ?>">
	<figure>
		<a href="<?php echo Url::toRoute(['view', 'slug' => $show->slug]) ?>" title="<?php echo $show->name; ?>">
			<img <?php echo $show->posterUrl; ?> alt="<?php echo Html::encode($show->name); ?>" title="<?php echo Html::encode($show->name); ?>">
		</a>

		<figcaption class="clearfix">
			<h4>
				<a href="<?php echo Url::toRoute(['view', 'slug' => $show->slug]) ?>" title="<?php echo $show->name; ?>">
					<?php echo Html::encode($show->name); ?>
				</a>
			</h4>

			<span class="last-seen">
				<?php if ($show->lastEpisode !== null): ?>
					<span title="<?php echo LanguageHelper::dateTime(strtotime($show->lastEpisode->created_at)); ?>">
						<?php echo $show->lastEpisode->createdAtAgo; ?>
					</span>
				<?php else: ?>
					<?php echo Yii::t('Show/Dashboard', 'Not seen yet'); ?>
				<?php endif; ?>
			</span>

			<?php if (!$archive): ?>
				<a class="btn btn-default" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tv/archive-show', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show/Dashboard', 'Archive {name}', ['name' => $show->name]); ?>">
					<span class="glyphicon glyphicon-lock"> <?php echo Yii::t('Show', 'Archive') ?></span>
				</a>
			<?php else: ?>
				<a class="btn btn-default" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tv/unarchive-show', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show/Dashboard', 'Remove {name} from the archive', ['name' => $show->name]); ?>">
					<span class="glyphicon glyphicon-arrow-left"> <?php echo Yii::t('Show', 'Restore') ?></span>
				</a>
			<?php endif; ?>
		</figcaption>
	</figure>
</li>
