<?php

use \Yii;
use \kartik\grid\GridView;

$this->title[] = Yii::t('Language', 'Languages');
?>

<h1><?php echo Yii::t('Language', 'Languages'); ?></h1>

<?php echo GridView::widget([
	'columns' => [
		'iso',
		'name',
		'en_name',
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{view} {update}',
		],
	],
	'dataProvider' => $dataProvider,
	'filterModel' => $filterModel,
]);
