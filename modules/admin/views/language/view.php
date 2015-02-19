<?php
use \yii\helpers\Url;
use \yii\widgets\DetailView;

$this->title[] = $model->en_name;
$this->title[] = Yii::t('Language', 'Languages');
?>

<h1><?php echo Yii::t('Language', 'Language {name}', [
	'name' => !empty($model->en_name) ? $model->en_name : $model->iso,
]); ?> <a class="btn btn-default" href="<?php echo Url::toRoute(['update', 'id' => $model->id]); ?>"><span class="glyphicon glyphicon-pencil"></span></a></h1>

<p>
	<a class="btn btn-default" href="<?php echo Url::toRoute(['index']); ?>"><?php echo Yii::t('Language', 'All Languages'); ?></a>
</p>

<?php
echo DetailView::widget([
	'model' => $model,
	'attributes' => [
		'id',
		'iso',
		'name',
		'en_name',
		'rtl:boolean',
		'hide:boolean',
		'popular_shows_updated_at:dateTime',
		'popular_movies_updated_at:dateTime',
		'created_at:dateTime',
		'updated_at:dateTime',
	],
]);
