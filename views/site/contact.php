<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\ContactForm $model
 */

use yii\helpers\Html;
use app\components\ActiveForm;
use yii\captcha\Captcha;

$this->title[] = Yii::t('Site/Contact', 'Contact');
?>
<div id="contact">
	<h1><?php echo Yii::t('Site/Contact', 'Contact'); ?></h1>

	<?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>
		<div class="alert alert-success">
			<?php echo Yii::t('Site/Contact', 'Contact'); ?>
		</div>
	<?php else: ?>

		<div class="row">
			<div class="col-md-6">
				<p>
					<?php echo Yii::t('Site/Contact', 'If you have any questions about this site, feature requests or found a bug, please feel free to contact us.'); ?>
				</p>

				<?php $form = ActiveForm::begin([
					'id' => 'contact-form',
				]); ?>
					<?php echo $form->field($model, 'name') ?>
					<?php echo $form->field($model, 'email') ?>
					<?php echo $form->field($model, 'subject') ?>
					<?php echo $form->field($model, 'body')->textArea(['rows' => 6]) ?>
					<?php echo $form->field($model, 'verifyCode')->widget(Captcha::className(), [
						'template' => '<div class="row"><div class="col-lg-3">{input}</div><div class="col-lg-3">{image}</div></div>',
					]) ?>

					<div class="form-group">
						<?php echo Html::submitButton(Yii::t('Site/Contact', 'Send'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
					</div>
				<?php ActiveForm::end(); ?>
			</div>
		</div>

	<?php endif; ?>
</div>
