<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;

$this->title[] = Yii::t('Profile/Index', 'Profile of {name}', [
	'name' => (!empty($user->name)) ? $user->name : $user->email,
]);
?>

<h1><?php echo Yii::t('Profile/Index', 'Profile of {name}', [
	'name' => (!empty($user->name)) ? $user->name : $user->email,
]); ?></h1>

<div class="row">
	<div class="col-sm-6">
		<h2><?php echo Yii::t('Profile/Index', 'Movies') ?></h2>
		<a href="<?php echo Yii::$app->urlManager->createUrl(['profile/movie', 'profile' => $user->profile_name]); ?>">
			<img <?php echo $movie->posterLargeAttribute; ?> alt="<?php echo Html::encode($movie->completeTitle); ?>" title="<?php echo Html::encode($movie->completeTitle); ?>">
		</a>
	</div>

	<div class="col-sm-6">
		<h2><?php echo Yii::t('Profile/Index', 'TV Shows') ?></h2>
		<a href="<?php echo Yii::$app->urlManager->createUrl(['profile/tv', 'profile' => $user->profile_name]); ?>">
			<img <?php echo $show->posterLargeAttribute; ?> alt="<?php echo Html::encode($show->completeName); ?>" title="<?php echo Html::encode($show->completeName); ?>">
		</a>
	</div>
</div>
