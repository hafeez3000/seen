<?php

use \yii\helpers\Url;

use \kartik\grid\GridView;

$this->title[] = Yii::t('Authorization', 'Authorization');
?>

<h1><?php echo Yii::t('Authorization', 'Authorization'); ?></h1>

<p>
	<a href="<?php echo Url::toRoute(['key/index']); ?>" class="btn btn-default"><?php echo Yii::t('Authorization', 'Keys') ?></a>
</p>

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
