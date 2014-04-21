<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\LoginForm $model
 */

use yii\helpers\Html;
use app\components\ActiveForm;

$this->title[] = Yii::t('Site/Login', 'Login');
?>
<div id="login">
	<h1><?php echo Yii::t('Site/Login', 'Login') ?></h1>

	<div class="row">
		<div class="col-md-6">
			<?php $form = ActiveForm::begin([
				'id' => 'login-form',
			]); ?>

			<?php echo $form->field($model, 'email') ?>

			<?php echo $form->field($model, 'password')->passwordInput() ?>

			<?php echo $form->field($model, 'rememberMe')->checkbox() ?>

			<div class="form-group">
				<?php echo Html::submitButton(Yii::t('Site/Login', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
			</div>

			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
