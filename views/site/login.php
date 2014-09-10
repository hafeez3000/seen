<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\LoginForm $model
 */

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\ActiveForm;

$this->title[] = Yii::t('Site/Login', 'Login');
?>
<div id="login">
	<h1><?php echo Yii::t('Site/Login', 'Login') ?></h1>

	<?php $form = ActiveForm::begin([
		'id' => 'login-form',
	]); ?>

	<?php echo $form->field($model, 'email') ?>

	<?php echo $form->field($model, 'password')->passwordInput() ?>

	<?php echo $form->field($model, 'rememberMe')->checkbox() ?>

	<div class="form-group">
		<div class="clearfix">
			<div class="pull-left">
				<?php echo Html::submitButton(Yii::t('Site/Login', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
			</div>

			<div class="pull-right">
				<a href="<?php echo Url::toRoute(['login/facebook']); ?>" class="btn btn-default" title="<?php echo Yii::t('Site/Login', 'Login with Facebook'); ?>"><span class="social facebook"></span> <?php echo Yii::t('Site/Login', 'Facebook'); ?></a>
			</div>
		</div>

		<div class="form-toolbar">
			<a href="<?php echo Url::toRoute(['reset']) ?>"><?php echo Yii::t('Site/Login', 'Forget your password?') ?></a>
			&nbsp;|&nbsp;
			<a href="<?php echo Url::toRoute(['sign-up']) ?>"><?php echo Yii::t('Site/Login', 'Don\'t have an account yet?') ?></a>
		</div>
	</div>

	<?php ActiveForm::end(); ?>
</div>
