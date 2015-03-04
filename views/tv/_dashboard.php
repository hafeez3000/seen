<?php
/**
 * @var yii\web\View $this
 */
?>

<div id="tv-<?php echo $active; ?>" class="tv-dashboard">
	<div id="tv-dashboard-header">
		<?php echo $this->render('_navigation', [
			'active' => $active,
			'title' => $title,
		]); ?>
	</div>

	<?php if (count($shows)): ?>
		<ul id="tv-dashboard-showlist" class="list-unstyled list-inline">
			<?php foreach ($shows as $show): ?>
				<?php echo $this->render('_view', [
					'show' => $show,
					'active' => $active,
				]); ?>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<div class="alert alert-info">
			<?php if ($active == 'dashboard'): ?>
				<?php echo Yii::t('Show/Dashboard', 'You are not subscribe to a TV Show! Start with searching for your favorites.'); ?>
			<?php elseif ($active == 'archive'): ?>
				<?php echo Yii::t('Show/Dashboard', 'Your archive is empty! Add TV Shows which are not in production anymore or you are currently not watching.'); ?>
			<?php elseif ($active == 'recommend'): ?>
				<?php echo Yii::t('Show/Dashboard', 'We currently cannot recommend any new shows for you! Try to subscribe to more shows you like.'); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
