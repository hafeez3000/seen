<?php
/**
 * @var yii\web\View $this
 * @var string $slug (optional)
 */

use \yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin([
	'action' => Yii::$app->urlManager->createAbsoluteUrl(['site/load']),
	'options' => [
		'class' => 'search-form',
		'data-search' => isset($slug) ? $slug : '',
		'data-tv-url' => Yii::$app->urlManager->createAbsoluteUrl(['tv/load']),
		'data-movie-url' => Yii::$app->urlManager->createAbsoluteUrl(['movie/load']),
		'data-person-url' => Yii::$app->urlManager->createAbsoluteUrl(['person/load']),
	]
]); ?>

	<input type="hidden" class="search" name="id" style="margin-top: 30px; width: 100%;">

<?php ActiveForm::end();
