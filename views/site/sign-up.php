<?php

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\SignupForm $model
 */

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\ActiveForm;

$this->title[] = Yii::t('Site/Signup', 'Sign Up');
?>

<div id="signup">
	<h1><?php echo Yii::t('Site/Signup', 'Sign Up') ?> <small><?php echo Yii::t('Site/Signup', 'for a free account') ?></small></h1>

	<?php $form = ActiveForm::begin([
		'id' => 'signup-form',
	]); ?>

	<?php echo $form->field($model, 'email') ?>

	<?php echo $form->field($model, 'password')->passwordInput() ?>

	<div class="form-group">
		<?php echo Html::submitButton(Yii::t('Site/Signup', 'Create your Account'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>&nbsp;
		<div class="form-toolbar">
			<a href="<?php echo Url::toRoute(['login']) ?>"><?php echo Yii::t('Site/Signup', 'Already have an account?') ?></a>
		</div>
	</div>

	<?php ActiveForm::end(); ?>
</div>