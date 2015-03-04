<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\LoginForm $model
 */

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

			<?php echo $form->field($model, 'email'); ?>

			<?php echo $form->field($model, 'name'); ?>

			<?php echo $form->field($model, 'language')->dropDownList($model->languages); ?>

			<?php echo $form->field($model, 'timezone')->dropDownList($model->timezones); ?>

			<?php echo $form->field($model, 'profile_public')->checkbox(); ?>

			<?php if (!empty($model->profile_name)): ?>
				<p class="text-muted">
					<a href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['profile/index', 'profile' => $model->profile_name]) ?>">
						<?php echo Yii::$app->urlManager->createAbsoluteUrl(['profile/index', 'profile' => $model->profile_name]) ?>
					</a>
				</p>
			<?php endif; ?>

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
