<?php
/**
 * @var yii\web\View $this
 */

echo $this->render('_dashboard', [
	'archive' => true,
	'title' => Yii::t('Show/Dashboard', 'Your archived TV Shows'),
	'shows' => $shows,
]);
