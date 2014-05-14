<?php
/**
 * @var yii\web\View $this
 */

use \yii\widgets\LinkPager;

$this->title[] = Yii::t('Email', 'Emails');
?>

<div id="email-index">
	<h1><?php echo Yii::t('Email', 'Emails'); ?></h1>

	<?php echo LinkPager::widget([
		'pagination' => $pages,
	]); ?>

	<?php foreach ($emails as $email): ?>
		<?php echo $this->render('_view', [
			'email' => $email,
		]) ?>
	<?php endforeach; ?>

	<?php echo LinkPager::widget([
		'pagination' => $pages,
	]); ?>
</div>