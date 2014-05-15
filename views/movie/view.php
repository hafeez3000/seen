<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

use \app\components\LanguageHelper;

$this->title[] = $movie->title;
$this->title[] = Yii::t('Movie/View', 'Movies');
?>

<h1>
	<div class="clearfix">
		<div class="pull-left"><?php echo Html::encode($movie->title); ?></div>
		<div class="pull-right">
            <?php if (count($userMovies) === 0): ?>
                <a href="<?php echo Url::toRoute(['watch', 'slug' => $movie->slug]); ?>" class="btn btn-sm btn-primary">
                    <?php echo Yii::t('Movie/View', 'Watched'); ?>
                </a>
            <?php else: ?>
                <a href="<?php echo Url::toRoute(['watch', 'slug' => $movie->slug]); ?>" class="btn btn-sm btn-default">
                    <?php echo Yii::t('Movie/View', 'Watched again'); ?>
                </a>
            <?php endif; ?>

            <?php if ($movie->onWatchlist): ?>
                <a href="<?php echo Url::toRoute(['watchlist/remove', 'slug' => $movie->slug]); ?>" class="btn btn-sm btn-default">
                    <?php echo Yii::t('Movie/View', 'Remove from watchlist'); ?>
                </a>
            <?php else: ?>
                <a href="<?php echo Url::toRoute(['watchlist/add', 'slug' => $movie->slug]); ?>" class="btn btn-sm btn-success">
                    <?php echo Yii::t('Movie/View', 'Add to watchlist'); ?>
                </a>
            <?php endif; ?>
        </div>
</h1>

<div id="movie-view">
	<div class="row">
		<div class="col-sm-4">
			<img <?php echo $movie->posterUrlLarge; ?> alt="<?php echo Html::encode($movie->title); ?>" title="<?php echo Html::encode($movie->title); ?>">
		</div>

		<div class="col-sm-8">
			<?php if (!empty($movie->overview)): ?>
				<div id="movie-view-overview">
					<?php echo Html::encode($movie->overview); ?>
				</div>
			<?php endif; ?>

			<?php if (count($userMovies)): ?>
				<div id="movie-view-watched">
					<h2><?php echo Yii::t('Movie/View', 'Watched {count} times', ['count' => count($userMovies)]); ?></h2>

					<ul id="movie-view-watched-list" class="list-unstyled list-inline">
						<?php foreach ($userMovies as $userMovie): ?>
							<li title="<?php echo LanguageHelper::dateTime(strtotime($userMovie->created_at)); ?>">
								<?php echo LanguageHelper::date(strtotime($userMovie->created_at)); ?>&nbsp;
								<a href="<?php echo Url::toRoute(['unwatch', 'id' => $userMovie->id]); ?>"><span class="glyphicon glyphicon-trash"></span></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if (count($movie->cast)): ?>
				<div id="movie-view-cast-wrapper" class="persons">
					<h2><?php echo Yii::t('Movie/View', 'Cast'); ?></h2>

					<ul id="movie-view-cast" class="list-unstyled list-inline">
						<?php foreach ($movie->cast as $cast): ?>
							<li>
								<a href="<?php echo Url::toRoute(['/person/view', 'id' => $cast->person->id]); ?>" title="<?php echo Html::encode(Yii::t('Movie', '{name} as {character}', [
									'name' => $cast->person->name,
									'character' => $cast->character,
								])); ?>">
									<img <?php echo $cast->person->profileUrl; ?> alt="<?php echo Html::encode(Yii::t('Movie', '{name} as {character}', [
										'name' => $cast->person->name,
										'character' => $cast->character,
									])); ?>">
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if (count($movie->crew)): ?>
				<div id="movie-view-crew-wrapper" class="persons">
					<h2><?php echo Yii::t('Movie/View', 'Crew'); ?></h2>

					<ul id="movie-view-crew" class="list-unstyled list-inline">
						<?php foreach ($movie->crew as $crew): ?>
							<li>
								<a href="<?php echo Url::toRoute(['/person/view', 'id' => $crew->person->id]); ?>" title="<?php echo Html::encode(Yii::t('Movie', '{job}: {name}', [
									'name' => $crew->person->name,
									'job' => $crew->job,
								])); ?>">
									<img <?php echo $crew->person->profileUrl; ?> alt="<?php echo Html::encode(Yii::t('Movie', '{job}: {name}', [
										'name' => $crew->person->name,
										'job' => $crew->job,
									])); ?>">
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if (count($movie->similarMovies)): ?>
				<div id="movie-view-similar-wrapper" class="persons">
					<h2><?php echo Yii::t('Movie/View', 'Similar Movies'); ?></h2>

					<ul id="movie-view-similar" class="list-unstyled list-inline">
						<?php foreach ($movie->similarMovies as $similarMovie): ?>
							<li class="<?php if (count($similarMovie->userWatches) > 0): ?>movie-view-similar-watched<?php endif; ?>">
								<a href="<?php echo Url::toRoute(['view', 'slug' => $similarMovie->slug]); ?>" title="<?php echo Html::encode($similarMovie->title); ?>">
									<img <?php echo $similarMovie->posterUrlSmall; ?> alt="<?php echo Html::encode($similarMovie->title); ?>">
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
