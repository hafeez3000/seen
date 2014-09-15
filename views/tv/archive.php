<?php
/**
 * @var yii\web\View $this
 */
$this->title[] = Yii::t('Show/Archive', 'TV Show Archive');

echo $this->render('_dashboard', [
	'archive' => true,
	'title' => Yii::t('Show/Dashboard', 'Your TV Archive'),
	'shows' => $shows,
]);
