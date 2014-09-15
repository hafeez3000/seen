<?php
/**
 * @var yii\web\View $this
 */
$this->title[] = Yii::t('Show/Dashboard', 'Recommendations');

echo $this->render('_dashboard', [
	'archive' => false,
	'title' => Yii::t('Show/Dashboard', 'Recommendations'),
	'shows' => $shows,
]);
