<?php

use \Yii;
use \kartik\grid\GridView;
use \yii\helpers\Url;

$this->title[] = Yii::t('User', 'Users');
?>

<h1><?php echo Yii::t('User', 'Users'); ?></h1>

<?php echo GridView::widget([
	'columns' => [
		'id',
		'email',
		'name',
		[
			'attribute' => 'language.en_name',
			'label' => Yii::t('User', 'Language'),
		],
		'timezone',
		'created_at',
		[
			'attribute' => 'movieCount',
			'value' => function($data) {
				return $data->getMovies()->count();
			}
		],
		[
			'attribute' => 'showCount',
			'value' => function($data) {
				return $data->getShows()->count();
			}
		],
		[
			'attribute' => 'episodeCount',
			'value' => function($data) {
				return $data->getUserEpisodes()->count();
			}
		],
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{view}',
		],
	],
	'dataProvider' => $dataProvider,
	'filterModel' => $filterModel,
]);
