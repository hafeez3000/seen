<?php
/**
 * @var yii\web\View $this
 */
$this->title[] = Yii::t('Show/Dashboard', 'Popular TV Shows');

echo $this->render('_dashboard', [
	'active' => 'popular',
	'title' => Yii::t('Show/Dashboard', 'Popular'),
	'shows' => $shows,
]);
