<?php
/**
 * @var yii\web\View $this
 */

use yii\widgets\ActiveForm;

$this->title[] = Yii::t('Log/Truncate', 'Truncate log table');
?>

<div id="log-index">
	<h1><?php echo Yii::t('Log/Truncate', 'Truncate log table'); ?></h1>

	<?php echo $this->render('_navigation'); ?>

	<?php $form = ActiveForm::begin(); ?>
		<button type="submit" class="btn btn-danger" name="confirm"><span class="glyphicon glyphicon-remove"></span> <?php echo Yii::t('Log/Truncate', 'Truncate') ?></button>
	<?php ActiveForm::end(); ?>
</div>
