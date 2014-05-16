<?php

use \yii\helpers\Html;

use \app\components\LanguageHelper;

?>

<table class="table table-striped table-condensed">
	<tbody>
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

		<?php if ($movie->vote_average !== null && $movie->vote_average > 0): ?>
			<tr>
				<td><?php echo Yii::t('Movie', 'Voting'); ?></td>
				<td><span title="<?php echo Yii::t('Movie', '{average}/10 ({count} Votes)', [
					'average' => $movie->vote_average,
					'count' => $movie->vote_count,
					]); ?>"><?php for ($i = 0; $i < round($movie->vote_average); $i++): ?><span class="glyphicon glyphicon-star"></span><?php endfor; ?></span></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
