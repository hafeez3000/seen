<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\LoginForm $model
 */

use yii\helpers\Html;
use app\components\ActiveForm;

$this->title[] = Yii::t('Site/Reset', 'Reset your password');
?>
<div id="reset">
	<h1><?php echo Yii::t('Site/Reset', 'Reset your password') ?></h1>

	<?php $form = ActiveForm::begin([
		'id' => 'reset-send-form',
	]); ?>

	<?php echo $form->field($model, 'email') ?>

	<div class="form-group">
		<?php echo Html::submitButton(Yii::t('Site/Reset', 'Reset Password'), ['class' => 'btn btn-primary', 'name' => 'reset-button']) ?>
	</div>

	<?php ActiveForm::end(); ?>
</div>
