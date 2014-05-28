<?php

use \Yii;
use \kartik\grid\GridView;
use \yii\helpers\Url;

$this->title[] = Yii::t('Authorization', 'Authorization');
?>

<h2><?php echo Yii::t('Authorization', 'Authorization'); ?></h2>

<?php echo GridView::widget([
	'columns' => [
		'name',
		'userCount',
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{view}',
		],
	],
	'dataProvider' => $dataProvider,
	'filterModel' => $filterModel,
]);
