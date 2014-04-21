<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
?>

<div id="tv-view">
	<div class="clearfix">
		<div class="pull-left">
			<h1>
				<?php echo Html::encode($show->name); ?>
				<?php if ($show->isUserSubscribed): ?>
					<a class="btn btn-default btn-sm" href="<?php echo Url::toRoute(['unsubscribe', 'slug' => $show->slug]); ?>"><?php echo Yii::t('Show', 'Unsubscribe'); ?></a>
				<?php else: ?>
					<a class="btn btn-primary btn-sm" href="<?php echo Url::toRoute(['subscribe', 'slug' => $show->slug]); ?>"><?php echo Yii::t('Show', 'Subscribe'); ?></a>
				<?php endif; ?>
			</h1>
		</div>

		<div class="pull-right" id="missing-information">
			<a href="https://www.themoviedb.org/tv/<?php echo $show->themoviedb_id; ?>?<?php echo http_build_query(['language' => $show->language->iso]) ?>" target="_blank"  title="<?php echo Yii::t('Season/View', 'Update information on The Movie Database') ?>">
				<?php echo Yii::t('Show/View', 'Missing information?') ?>
			</a>
		</div>
	</div>

	<div id="tv-view-content" class="row">
		<div id="tv-view-information" class="col-sm-6 col-md-5 col-lg-4">
			<?php if (!empty($show->backdrop_path)): ?>
				<div id="tv-view-backdrop">
					<img src="<?php echo $show->backdropUrl; ?>">
				</div>
			<?php endif; ?>

			<?php if (!empty($show->overview)): ?>
				<div id="tv-view-overview">
					<?php echo Html::encode($show->overview); ?>
				</div>
			<?php endif; ?>

			<?php if (count($show->cast)): ?>
				<div id="tv-view-cast-wrapper" class="persons">
					<h2><?php echo Yii::t('Show/View', 'Cast'); ?></h2>

					<ul id="tv-view-cast" class="list-unstyled list-inline">
						<?php foreach ($show->cast as $cast): ?>
							<li>
								<img src="<?php echo $cast->profileUrl; ?>" alt="<?php echo Html::encode($cast->name); ?>" title="<?php echo Html::encode($cast->name); ?>">
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if (count($show->crew)): ?>
				<div id="tv-view-crew-wrapper" class="persons">
					<h2><?php echo Yii::t('Show/View', 'Crew'); ?></h2>

					<div id="tv-view-crew" class="list-unstyled">
						<?php foreach ($show->crew as $crew): ?>
							<?php echo Html::encode($crew->name); ?>,&nbsp;
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<div id="tv-view-seasons" class="col-sm-6 col-md-7 col-lg-8" data-check-url="<?php echo Yii::$app->urlManager->createAbsoluteUrl('episode/seen'); ?>" data-uncheck-url="<?php echo Yii::$app->urlManager->createAbsoluteUrl('episode/unseen'); ?>">

			<?php foreach ($show->seasons as $season): ?>
				<div id="tv-view-season-<?php echo $season->id; ?>" class="tv-view-season panel panel-default" data-number="<?php echo $season->number; ?>">
					<div class="panel-heading">
						<div class="clearfix">
							<div class="pull-left">
								<h3><?php echo $season->fullName; ?></h3>
							</div>

							<div class="pull-right">
								<a href="#" class="mark-season-seen" data-id="<?php echo $season->id; ?>" title="<?php echo Yii::t('Show/View', 'Mark complete season as seen'); ?>"><span class="glyphicon glyphicon-ok"></span></a>&nbsp;
								<a href="https://www.themoviedb.org/tv/<?php echo $show->themoviedb_id; ?>/season/<?php echo $season->number; ?>?<?php echo http_build_query(['language' => $show->language->iso]) ?>" target="_blank" title="<?php echo Yii::t('Show/View', 'Edit missing information on The Movie Database'); ?>"><span class="glyphicon glyphicon-pencil"></span></a>
							</div>
						</div>
					</div>

					<?php $episodesSeen = $season->userEpisodesSeen; ?>

					<div class="panel-body">
						<ul class="tv-view-episodes list-unstyled list-inline">
							<?php foreach ($season->episodes as $episode): ?>
								<li class="<?php if (isset($episodesSeen[$episode->id])): ?>has-seen<?php endif; ?>" data-id="<?php echo $episode->id; ?>" data-seen="<?php if (isset($episodesSeen[$episode->id])): ?>1<?php else: ?>0<?php endif; ?>" title="<?php echo Yii::t('Show/View', 'Mark `{name}` as seen', ['name' => $episode->fullName]); ?>">
									<?php echo Html::encode($episode->fullName); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			<?php endforeach; ?>

		</div>
	</div>
</div>