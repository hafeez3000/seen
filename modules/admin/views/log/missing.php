<?php
/**
 * @var yii\web\View $this
 */

$this->title[] = Yii::t('Log/Index', 'Log messages');
?>

<div id="log-index" class="log-important">
	<h1><?php echo Yii::t('Log', '404 errors'); ?></h1>

	<?php echo $this->render('_index', [
		'logs' => $logs,
		'pages' => $pages,
	]); ?>
</div>
