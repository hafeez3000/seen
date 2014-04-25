<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\LoginForm $model
 */

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\ActiveForm;

$this->title[] = Yii::t('User/Account', 'Your Account');
?>
<div id="account">
	<h1><?php echo Yii::t('User/Account', 'Your Account') ?></h1>

	<div class="row">
		<div class="col-md-6">
			<?php $form = ActiveForm::begin([
				'id' => 'account-form',
				'options' => [
					'autocomplete' => 'off',
				],
			]); ?>

			<?php echo $form->field($model, 'email') ?>

			<?php echo $form->field($model, 'language')->dropDownList($model->languages); ?>

			<?php echo $form->field($model, 'password')->passwordInput([
				'autocomplete' => 'off',
			]); ?>

			<div class="form-group">
				<?php echo Html::submitButton(Yii::t('User/Account', 'Save Settings'), ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
			</div>

			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
