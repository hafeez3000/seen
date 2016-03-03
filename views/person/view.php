<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

use \app\components\LanguageHelper;

$this->title[] = $person->name;
$this->title[] = Yii::t('Person/View', 'Persons');
?>

<div id="person-view">
	<h1><?php echo Html::encode($person->name); ?></h1>

	<div class="row">
		<div class="col-sm-4 col-md-3 col-lg-2">
			<img <?php echo $person->profileMediumAttribute; ?>>
		</div>

		<div class="col-sm-8 col-md-9 col-lg-10">
			<?php if (!empty($person->biography)): ?>
				<p><?php echo Html::encode($person->biography); ?></p>
			<?php else: ?>
				<?php if ($person->updated_at !== null): ?>
					<div class="alert alert-info"><?php echo Yii::t('Person/View', 'Currently theire is no description for this person available. Help by <a href="{url}">creating one</a> at The Movie Database.', [
						'url' => 'https://www.themoviedb.org/person/' . $person->id,
					]); ?></div>
				<?php else: ?>
					<div class="alert alert-info"><?php echo Yii::t('Person/View', 'Currently theire is no description for this person available.'); ?></div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (!empty($person->birthday) || !empty($perosn->deathday) || !empty($person->homepage)): ?>
				<div class="row">
					<div class="col-md-6"
>						<table class="table table-stripped">
							<tbody>
								<?php if (!empty($person->birthday)): ?>
									<tr>
										<td><strong><?php echo Yii::t('Person/View', 'Birthday'); ?></strong></td>
										<td><?php echo LanguageHelper::date(strtotime($person->birthday)); ?></td>
									</tr>
								<?php endif; ?>
								<?php if (!empty($person->deathday)): ?>
									<tr>
										<td><strong><?php echo Yii::t('Person/View', 'Deathday'); ?></strong></td>
										<td><?php echo LanguageHelper::date(strtotime($person->deathday)); ?></td>
									</tr>
								<?php endif; ?>
								<?php if (!empty($person->homepage)): ?>
									<tr>
										<td><strong><?php echo Yii::t('Person/View', 'Homepage'); ?></strong></td>
										<td><?php echo Yii::$app->formatter->asUrl($person->homepage); ?></td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			<?php endif; ?>


			<?php if (count($movies) > 0): ?>
				<h2><?php echo Yii::t('Person/View', 'Movies with/from {name}', ['name' => $person->name]); ?></h2>

				<div class="row" id="person-view-movies">
					<?php foreach ($movies as $movie): ?>
						<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 <?php if (count($movie->userWatches) > 0): ?>person-view-movie-watched<?php endif; ?>">
							<a href="<?php echo Url::toRoute(['movie/view', 'slug' => $movie->slug]); ?>" title="<?php echo Html::encode($movie->completeTitle); ?>">
								<img <?php echo $movie->posterLargeAttribute; ?>>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if (count($shows) > 0): ?>
				<h2><?php echo Yii::t('Person/View', 'TV Shows with/from {name}', ['name' => $person->name]); ?></h2>

				<div class="row" id="person-view-shows">
					<?php foreach ($shows as $show): ?>
						<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 <?php if ($show->isUserSubscribed): ?>person-view-show-subscribed<?php endif; ?>">
							<a href="<?php echo Url::toRoute(['tv/view', 'slug' => $show->slug]); ?>" title="<?php echo Html::encode($show->name); ?>">
								<img <?php echo $show->posterLargeAttribute; ?>>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
