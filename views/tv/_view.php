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
		<a href="<?php echo Url::toRoute(['tv/view', 'slug' => $show->slug]) ?>" title="<?php echo $show->completeName; ?>">
			<img <?php echo $show->posterUrl; ?> alt="<?php echo Html::encode($show->completeName); ?>" title="<?php echo Html::encode($show->completeName); ?>">
		</a>

		<figcaption class="clearfix">
			<h4>
				<a href="<?php echo Url::toRoute(['tv/view', 'slug' => $show->slug]) ?>" title="<?php echo $show->completeName; ?>">
					<?php echo Html::encode($show->completeName); ?>
				</a>
			</h4>

			<a href="<?php echo Url::toRoute(['tv/view', 'slug' => $show->slug]) ?>" title="<?php echo $show->completeName; ?>" class="last-seen">
				<?php if ($show->getLastEpisode(null, true) !== null): ?>
					<span title="<?php echo LanguageHelper::dateTime(strtotime($show->getLastEpisode(null, true)->created_at)); ?>">
						<?php echo $show->getLastEpisode(null, true)->createdAtAgo; ?>
					</span>
				<?php else: ?>
					<?php echo Yii::t('Show/Dashboard', 'Not seen yet'); ?>
				<?php endif; ?>
			</a>

			<?php if ($active == 'dashboard'): ?>
				<a class="btn btn-default" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tv/archive-show', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show/Dashboard', 'Archive {name}', ['name' => $show->completeName]); ?>">
					<span class="glyphicon glyphicon-lock"> <?php echo Yii::t('Show', 'Archive') ?></span>
				</a>
			<?php elseif ($active == 'archive'): ?>
				<a class="btn btn-default" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tv/unarchive-show', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show/Dashboard', 'Remove {name} from the archive', ['name' => $show->completeName]); ?>">
					<span class="glyphicon glyphicon-arrow-left"> <?php echo Yii::t('Show', 'Restore') ?></span>
				</a>
			<?php elseif ($active == 'popular'): ?>
				<a class="btn btn-default" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tv/view', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show/Dashboard', 'Remove {name} from the archive', ['name' => $show->completeName]); ?>">
					<span class="glyphicon glyphicon-search"> <?php echo Yii::t('Show', 'Show') ?></span>
				</a>
			<?php elseif ($active == 'recommend'): ?>
				<a class="btn btn-primary" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tv/subscribe', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show/Dashboard', 'Remove {name} from the archive', ['name' => $show->completeName]); ?>">
					<span class="glyphicon glyphicon-ok"> <?php echo Yii::t('Show', 'Subscribe') ?></span>
				</a>
			<?php endif; ?>
		</figcaption>
	</figure>
</li>
