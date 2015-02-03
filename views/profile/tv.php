<?php
/**
 * @var yii\web\View $this
 */

use \yii\widgets\ActiveForm;
use \yii\helpers\Url;
use \yii\helpers\Html;

use \app\components\LanguageHelper;

$this->title[] = Yii::t('Profile/Tv', '{name} TV Shows', [
	'name' => (!empty($user->name)) ? $user->name : $user->email,
]);
?>

<div id="tv-dashboard" class="tv-dashboard">
	<div class="row" id="tv-dashboard-header">
		<div class="col-sm-6 col-md-8">
			<h1>
				<small><a href="<?php echo Url::toRoute(['profile/index', 'profile' => $user->profile_name]); ?>"><span class="glyphicon glyphicon-arrow-left"></span></a></small>

				<?php echo Yii::t('Profile/Tv', '{name} TV Shows', [
					'name' => (!empty($user->name)) ? $user->name : $user->email,
				]); ?>
			</h1>
		</div>

		<div class="col-sm-6 col-md-4">
			<?php echo $this->render('/site/_search'); ?>
		</div>
	</div>

	<?php if (count($shows)): ?>
		<ul id="tv-dashboard-showlist" class="list-unstyled list-inline">
			<?php foreach ($shows as $show): ?>
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
								<?php if ($show->getLastEpisode($user->id)->one() !== null): ?>
									<span title="<?php echo LanguageHelper::dateTime(strtotime($show->getLastEpisode($user->id)->one()->created_at)); ?>">
										<?php echo $show->getLastEpisode($user->id)->one()->createdAtAgo; ?>
									</span>
								<?php else: ?>
									<?php echo Yii::t('Show/Dashboard', 'Not seen yet'); ?>
								<?php endif; ?>
							</a>

							<a class="btn btn-default" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tv/archive-show', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show/Dashboard', 'Archive {name}', ['name' => $show->completeName]); ?>">
								<span class="glyphicon glyphicon-lock"> <?php echo Yii::t('Show', 'Archive') ?></span>
							</a>
						</figcaption>
					</figure>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<div class="alert alert-info">
			<?php if ($active == 'dashboard'): ?>
				<?php echo Yii::t('Profile/Tv', 'You are not subscribe to a TV Show! Start with searching for your favorites.'); ?>
			<?php elseif ($active == 'archive'): ?>
				<?php echo Yii::t('Profile/Tv', 'Your archive is empty! Add TV Shows which are not in production anymore or you are currently not watching.'); ?>
			<?php elseif ($active == 'recommend'): ?>
				<?php echo Yii::t('Profile/Tv', 'We currently cannot recommend any new shows for you! Try to subscribe to more shows you like.'); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
