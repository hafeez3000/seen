<?php

use \yii\helpers\Html;
use \yii\helpers\Url;

use \app\components\LanguageHelper;

?>

<table class="table table-striped table-condensed">
	<tbody>
		<?php if (!empty($movie->original_title)): ?>
			<tr>
				<td><?php echo Yii::t('Movie', 'Original Title'); ?></td>
				<td title="<?php echo Html::encode($movie->original_title); ?>"><?php echo Html::encode($movie->original_title); ?></td>
			</tr>
		<?php endif; ?>

		<?php if (!empty($movie->status)): ?>
			<tr>
				<td><?php echo Yii::t('Movie', 'Status'); ?></td>
				<td><?php echo Yii::t('Movie', Html::encode($movie->status)); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ($movie->release_date !== null): ?>
			<tr>
				<td><?php echo Yii::t('Movie', 'Released'); ?></td>
				<td><?php echo LanguageHelper::date(strtotime($movie->release_date)); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ($movie->runtime !== null && $movie->runtime > 0): ?>
			<tr>
				<td><?php echo Yii::t('Movie', 'Runtime'); ?></td>
				<td><?php echo LanguageHelper::number($movie->runtime); ?>&nbsp;<?php echo Yii::t('Movie', 'Minutes') ?></td>
			</tr>
		<?php endif; ?>

		<?php if ($movie->budget !== null && $movie->budget > 0): ?>
			<tr>
				<td><?php echo Yii::t('Movie', 'Budget'); ?></td>
				<td><?php echo LanguageHelper::number($movie->budget); ?>&nbsp;&dollar;</td>
			</tr>
		<?php endif; ?>

		<?php if ($movie->revenue !== null && $movie->revenue > 0): ?>
			<tr>
				<td><?php echo Yii::t('Movie', 'Revenue'); ?></td>
				<td><?php echo LanguageHelper::number($movie->revenue); ?>&nbsp;&dollar;</td>
			</tr>
		<?php endif; ?>

		<?php if (!empty($movie->homepage)): ?>
			<tr>
				<td><?php echo Yii::t('Movie', 'Website'); ?></td>
				<td ><a class="movie-view-details-cut" href="<?php echo Html::encode($movie->homepage); ?>" title="<?php echo Html::encode($movie->homepage); ?>"><?php echo Html::encode($movie->homepage); ?></a></td>
			</tr>
		<?php endif; ?>

		<?php if (count($movie->genres > 0)): ?>
			<tr>
				<td><?php echo Yii::t('Movie', 'Genres'); ?></td>
				<td class="breakable movie-view-details-genres">
					<?php foreach ($movie->genres as $genre): ?>
						<span class="label label-default"><?php echo Html::encode($genre->name); ?></span>
					<?php endforeach; ?>
				</td>
			</tr>
		<?php endif; ?>

		<tr>
			<td><?php echo Yii::t('Movie', 'Voting'); ?></td>
			<td>
				<span title="<?php echo Yii::t('Movie', '{average}/10 ({count} Votes)', [
					'average' => $movie->vote_average,
					'count' => $movie->vote_count,
					]); ?>" class="movie-rating rating">
					<?php $voteAverage = ($userRating !== null) ? $userRating->rating : round($movie->vote_average); ?>
					<?php for ($i = 0; $i < $voteAverage; $i++): ?>
						<a
							href="<?php echo Yii::$app->urlManager->createUrl(['/movie/rate', 'slug' => $movie->slug, 'rating' => $i + 1]); ?>"
							title="<?php echo Yii::t('Movie', 'Rate with {count} stars', ['count' => $i + 1]); ?>"
							class="<?php echo ($userRating !== null) ? 'rating-user' : ''; ?>">
							<span class="glyphicon glyphicon-star"></span>
						</a>
					<?php endfor; ?>
					<?php for ($i = $voteAverage; $i < 10; $i++): ?>
						<a
							href="<?php echo Yii::$app->urlManager->createUrl(['/movie/rate', 'slug' => $movie->slug, 'rating' => $i + 1]); ?>"
							title="<?php echo Yii::t('Movie', 'Rate with {count} stars', ['count' => $i + 1]); ?>">
							<span class="glyphicon glyphicon-star-empty"></span>
						</a>
					<?php endfor; ?>
				</span>
			</td>
		</tr>

		<?php if (count($userMovies)): ?>
			<tr>
				<?php if (count($userMovies) == 1): ?>
					<td><?php echo Yii::t('Movie/View', 'Watched {count} time', ['count' => count($userMovies)]); ?></td>
				<?php else: ?>
					<td><?php echo Yii::t('Movie/View', 'Watched {count} times', ['count' => count($userMovies)]); ?></td>
				<?php endif; ?>

				<td>
					<ul id="movie-view-watched-list" class="list-unstyled list-inline">
						<?php foreach ($userMovies as $userMovie): ?>
							<li title="<?php echo LanguageHelper::dateTime(strtotime($userMovie->created_at)); ?>">
								<?php echo LanguageHelper::date(strtotime($userMovie->created_at)); ?>&nbsp;
								<a href="<?php echo Url::toRoute(['unwatch', 'id' => $userMovie->id]); ?>"><span class="glyphicon glyphicon-trash"></span></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</td>
			</div>
		<?php endif; ?>
	</tbody>
</table>
