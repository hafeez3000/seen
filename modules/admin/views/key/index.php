<?php

use \yii\helpers\Url;

use \kartik\grid\GridView;

$this->title[] = Yii::t('Keys', 'Keys');
?>

<h1><?php echo Yii::t('Keys', 'Keys'); ?></h1>

<p>
	<a href="<?php echo Url::toRoute(['generate']); ?>" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> <?php echo Yii::t('Keys', 'Generate'); ?></a>
</p>

<?php echo GridView::widget([
	'columns' => [
		'id',
		'user_id',
		'key',
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '',
		],
	],
	'dataProvider' => $dataProvider,
	'filterModel' => $filterModel,
]);
