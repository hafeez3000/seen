<?php
/**
 * @var yii\web\View $this
 */
?>

<div id="log-index">
	<h1><?php echo Yii::t('Log', 'Log messages'); ?></h1>

	<?php echo $this->render('_index', [
		'logs' => $logs,
		'pages' => $pages,
	]); ?>
</div>