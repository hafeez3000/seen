<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

use \app\components\LanguageHelper;

$this->title[] = $show->completeName;
$this->title[] = Yii::t('Show/View', 'TV Shows');
?>

<div id="tv-view" data-subscribed="<?php echo $show->isUserSubscribed ? 1 : 0; ?>">
	<?php if ($showNative !== null): ?>
		<div class="alert alert-info">
			<?php echo Yii::t('Movie', 'The Show is also available in <a href="{url}" title="{title}">{language}</a>', [
				'url' => Url::toRoute(['view', 'slug' => $showNative->slug]),
				'title' => $showNative->name,
				'language' => $showNative->language->name,
			]); ?>
		</div>
	<?php endif; ?>

	<h1>
		<?php echo Html::encode($show->completeName); ?>
		<?php if ($show->isUserSubscribed): ?>
			<a class="btn btn-default btn-sm" href="<?php echo Url::toRoute(['unsubscribe', 'slug' => $show->slug]); ?>"><?php echo Yii::t('Show', 'Unsubscribe'); ?></a>
		<?php else: ?>
			<a class="btn btn-primary btn-sm" href="<?php echo Url::toRoute(['subscribe', 'slug' => $show->slug]); ?>"><?php echo Yii::t('Show', 'Subscribe'); ?></a>
		<?php endif; ?>

		<?php if (!Yii::$app->user->isGuest): ?>
			<?php if ($show->isArchived()): ?>
				<a class="btn btn-default btn-sm" href="<?php echo Url::toRoute(['unarchive-show', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show', 'Restore from archive'); ?>"><span class="glyphicon glyphicon-arrow-left"></span></a>
			<?php else: ?>
				<a class="btn btn-default btn-sm" href="<?php echo Url::toRoute(['archive-show', 'slug' => $show->slug]); ?>" title="<?php echo Yii::t('Show', 'Move to archive'); ?>"><span class="glyphicon glyphicon-lock"></span></a>
			<?php endif; ?>
		<?php endif; ?>

		<?php if (Yii::$app->user->can('admin')): ?>
			<a class="btn btn-default btn-sm" data-loading-text="<?php echo Yii::t('Show', 'Syncing...') ?>" id="show-sync" data-url="<?php echo Url::toRoute(['sync', 'themoviedbId' => $show->themoviedb_id]); ?>"><?php echo Yii::t('Show', 'Sync') ?></a>
		<?php endif; ?>
	</h1>

	<div id="tv-view-content" class="row">
		<div id="tv-view-information" class="col-sm-6 col-md-5 col-lg-4">
			<div id="tv-view-backdrop">
				<img <?php echo $show->backdropUrl; ?> alt="<?php echo Html::encode($show->completeName); ?>">
			</div>

			<?php if (!empty($show->overview)): ?>
				<div id="tv-view-overview">
					<?php echo Html::encode($show->overview); ?>
				</div>
			<?php endif; ?>

			<div id="tv-view-details">
				<table class="table table-striped table-condensed">
					<?php if (!empty($show->original_name)): ?>
						<tr>
							<td><?php echo Yii::t('Show', 'Original Name'); ?></td>
							<td><?php echo Html::encode($show->original_name); ?></td>
						</tr>
					<?php endif; ?>

					<?php if ($show->first_air_date !== null): ?>
						<tr>
							<td><?php echo Yii::t('Show', 'First aired'); ?></td>
							<td><?php echo LanguageHelper::date(strtotime($show->first_air_date)); ?></td>
						</tr>
					<?php endif; ?>

					<?php if ($show->last_air_date !== null): ?>
						<tr>
							<td><?php echo Yii::t('Show', 'Last aired'); ?></td>
							<td><?php echo LanguageHelper::date(strtotime($show->last_air_date)); ?></td>
						</tr>
					<?php endif; ?>

					<?php if ($show->in_production !== null && $show->in_production !== ''): ?>
						<tr>
							<td><?php echo Yii::t('Show', 'In Production'); ?></td>
							<td><?php echo $show->in_production ? Yii::t('Show', 'Yes') : Yii::t('Show', 'No'); ?></td>
						</tr>
					<?php endif; ?>

					<?php if (!empty($show->status)): ?>
						<tr>
							<td><?php echo Yii::t('Show', 'Status'); ?></td>
							<td><?php echo Html::encode(Yii::t('Show', $show->status)); ?></td>
						</tr>
					<?php endif; ?>

					<?php if (!empty($show->homepage)): ?>
						<tr>
							<td><?php echo Yii::t('Show', 'Website'); ?></td>
							<td><a href="<?php echo $show->homepage; ?>" title="<?php echo $show->homepage; ?>"><?php echo Html::encode($show->homepage); ?></a></td>
						</tr>
					<?php endif; ?>

					<?php if (count($show->genres > 0)): ?>
						<tr>
							<td><?php echo Yii::t('Show', 'Genres'); ?></td>
							<td class="breakable tv-view-details-genres">
								<?php foreach ($show->genres as $genre): ?>
									<span class="label label-default"><?php echo Html::encode($genre->name); ?></span>&nbsp;
								<?php endforeach; ?>
							</td>
						</tr>
					<?php endif; ?>

					<?php if ($show->vote_count > 0): ?>
						<tr>
							<td><?php echo Yii::t('Show/View', '{average} out of 10', [
								'average' => $show->vote_average,
							]); ?></td>
							<td>
								<span title="<?php echo Yii::t('Show/View', '{average}/10 ({count} Votes)', [
										'average' => $show->vote_average,
										'count' => $show->vote_count,
									]); ?>">
									<?php $voteAverage = round($show->vote_average); ?>
									<?php for ($i = 0; $i < $voteAverage; $i++): ?>
										<span class="glyphicon glyphicon-star"></span>
									<?php endfor; ?>
									<?php for ($i = $voteAverage; $i < 10; $i++): ?>
										<span class="glyphicon glyphicon-star-empty"></span>
									<?php endfor; ?>
								</span>
							</td>
						</tr>
					<?php endif; ?>
				</table>
			</div>

			<div id="tv-view-ads">
				<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<ins class="adsbygoogle"
					style="display:block"
					data-ad-client="<?php echo Yii::$app->params['adsense']['client']; ?>"
					data-ad-slot="<?php echo Yii::$app->params['adsense']['slot']; ?>"
					data-ad-format="auto"></ins>
				<script>
				(adsbygoogle = window.adsbygoogle || []).push({});
				</script>
			</div>

			<?php if (count($show->videos)): ?>
				<div id="tv-view-videos">
					<h2><?php echo Yii::t('Show/View', 'Videos'); ?></h2>

					<?php foreach ($show->videos as $video): if ($video->site == 'YouTube'): ?>
						<div class="embed-responsive embed-responsive-16by9">
							<iframe src="//www.youtube.com/embed/<?php echo $video->key; ?>" allowfullscreen></iframe>
						</div>
					<?php endif; endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if (count($show->cast)): ?>
				<div id="tv-view-cast-wrapper" class="persons">
					<h2><?php echo Yii::t('Show/View', 'Cast'); ?></h2>

					<ul id="tv-view-cast" class="list-unstyled list-inline">
						<?php foreach ($show->cast as $cast): ?>
							<li>
								<a href="<?php echo Url::toRoute(['/person/view', 'id' => $cast->person->id]); ?>" title="<?php echo (!empty($cast->character)) ? Html::encode(Yii::t('Show/View',
									'{name} as {character}',
									[
										'name' => $cast->person->name,
										'character' => $cast->character,
									]
								)) : Html::encode($cast->person->name); ?>">
									<img <?php echo $cast->person->profileUrl; ?> alt="<?php echo Html::encode(Yii::t('Show/View', '{name} as {character}', [
										'name' => $cast->person->name,
										'character' => $cast->character,
									])); ?>">
								</a>
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
							<a href="<?php echo Url::toRoute(['/person/view', 'id' => $crew->person->id]); ?>"><?php echo Html::encode($crew->person->name); ?></a>,&nbsp;
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<div id="tv-view-seasons" class="col-sm-6 col-md-7 col-lg-8" data-check-url="<?php echo Yii::$app->urlManager->createAbsoluteUrl('episode/seen'); ?>" data-uncheck-url="<?php echo Yii::$app->urlManager->createAbsoluteUrl('episode/unseen'); ?>">

			<?php $episodesSeen = $show->userEpisodesSeen; ?>

			<?php foreach ($show->seasons as $season): ?>
				<?php $seasonComplete = true; ?>
				<?php foreach ($season->episodes as $episode): ?>
					<?php if (!$show->isUserSubscribed || !isset($episodesSeen[$episode->id])): ?>
						<?php $seasonComplete = false; ?>
						<?php break; ?>
					<?php endif; ?>
				<?php endforeach; ?>

				<div id="tv-view-season-<?php echo $season->id; ?>" class="tv-view-season panel panel-default" data-number="<?php echo $season->number; ?>">
					<div class="panel-heading">
						<div class="clearfix">
							<div class="pull-left">
								<h3>
									<a class="link" data-toggle="collapse" data-target="#tv-view-season-<?php echo $season->id; ?>-body">
										<?php echo $season->fullName; ?>
									</a>
								</h3>
							</div>

							<div class="pull-right">
								<?php if ($show->isUserSubscribed): ?>
									<a href="#" class="mark-season-seen" data-id="<?php echo $season->id; ?>" title="<?php echo Yii::t('Show/View', 'Label complete season as seen'); ?>"><span class="glyphicon glyphicon-ok"></span></a>&nbsp;
								<?php endif; ?>
								<a href="https://www.themoviedb.org/tv/<?php echo $show->themoviedb_id; ?>/season/<?php echo $season->number; ?>?<?php echo http_build_query(['language' => $show->language->iso]) ?>" target="_blank" title="<?php echo Yii::t('Show/View', 'Edit missing information on The Movie Database'); ?>"><span class="glyphicon glyphicon-pencil"></span></a>
							</div>
						</div>
					</div>

					<div class="panel-body collapse <?php echo ($seasonComplete || $season->number == 0) ? '' : 'in'; ?>" id="tv-view-season-<?php echo $season->id; ?>-body">
						<ul class="tv-view-episodes list-unstyled list-inline">
							<?php foreach ($season->episodes as $episode): ?>
								<?php if ($show->isUserSubscribed): ?>
									<li><a class="<?php if ($show->isUserSubscribed && isset($episodesSeen[$episode->id])): ?>has-seen<?php endif; ?>"
										href="<?php echo ($show->isUserSubscribed && isset($episodesSeen[$episode->id])) ? Yii::$app->urlManager->createUrl(['episode/unseen', 'id' => $episode->id]) : Yii::$app->urlManager->createUrl(['episode/seen', 'id' => $episode->id]); ?>"
										data-id="<?php echo $episode->id; ?>"
										data-seen="<?php if (isset($episodesSeen[$episode->id])): ?>1<?php else: ?>0<?php endif; ?>"
										title="<?php echo ($show->isUserSubscribed && isset($episodesSeen[$episode->id])) ? Yii::t('Show/View', 'Label `{name}` as unseen', ['name' => $episode->fullName]) : Yii::t('Show/View', 'Label `{name}` as seen', ['name' => $episode->fullName]); ?>">
										<?php echo Html::encode($episode->fullName); ?>
									</a></li>
								<?php elseif (!Yii::$app->user->isGuest): ?>
									<li><a
										href="#"
										onclick="App.warning('<?php echo Yii::t('Show/View', 'Please subscribe to the show before labeling episodes as seen.'); ?>')"
										title="<?php echo Yii::t('Show/View', 'Subscribe to the show for labeling `{name}` as seen', ['name' => $episode->fullName]); ?>">
										<?php echo Html::encode($episode->fullName); ?>
									</a></li>
								<?php else: ?>
									<li><a
										href="<?php echo Yii::$app->urlManager->createUrl('site/login'); ?>"
										title="<?php echo Yii::t('Show/View', 'Login to label `{name}` as seen', ['name' => $episode->fullName]); ?>">
										<?php echo Html::encode($episode->fullName); ?>
									</a></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			<?php endforeach; ?>

		</div>
	</div>
</div>
