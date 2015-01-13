<?php
use \yii\helpers\Url;
use \yii\widgets\DetailView;

$this->title[] = $model->email;
$this->title[] = Yii::t('User', 'Users');
?>

<h2><?php echo Yii::t('User', 'User {name}', [
	'name' => !empty($model->name) ? $model->name : $model->email,
]); ?></h2>

<p>
	<a class="btn btn-default" href="<?php echo Url::toRoute(['index']); ?>"><?php echo Yii::t('User', 'All Users'); ?></a>
</p>

<?php
echo DetailView::widget([
	'model' => $model,
	'attributes' => [
		'id',
		'email',
		'name',
		'language.en_name',
		'timezone',
		'created_at',
		'updated_at',
		'deleted_at',
	],
]);
?>

<div class="row">
	<div class="col-md-6">
		<h2><?php echo Yii::t('User', '{count} shows', ['count' => count($model->allShows)]); ?></h2>

		<ul>
			<?php foreach ($model->allShows as $show): ?>
				<li><a href="<?php echo Url::toRoute(['/tv/view', 'slug' => $show->slug]); ?>"><?php echo $show->original_name; ?> - <?php echo $show->completeName; ?> (<?php echo $show->getAllUserEpisodes($model->id)->count(); ?>)</a></li>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="col-md-6">
		<h2><?php echo Yii::t('User', '{count} movies', ['count' => count($model->movies)]); ?></h2>

		<ul>
			<?php foreach ($model->movies as $movie): ?>
				<li><a href="<?php echo Url::toRoute(['/movie/view', 'slug' => $movie->slug]); ?>"><?php echo $movie->original_title; ?> (<?php echo $movie->completeTitle; ?>)</a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
