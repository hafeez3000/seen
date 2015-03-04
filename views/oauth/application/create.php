<?php
/**
 * @var yii\web\View $this
 */

use \yii\widgets\ActiveForm;
use \yii\helpers\Html;

$this->title[] = Yii::t('Oauth/Application', 'Create Applications');
$this->title[] = Yii::t('Developer', 'Developer');
?>

<?php echo $this->render('/developer/_header', [
	'active' => 'oauth-application/create',
]); ?>


<h1><?php echo Yii::t('Oauth/Application', 'Create application'); ?></h1>

<div class="row">
	<div class="col-md-6">
		<?php $form = ActiveForm::begin([
			'id' => 'oauth-application-form',
		]); ?>

			<?php echo $form->field($model, 'name')->textInput()->hint(Yii::t('Oauth/Application', 'The name of the application will be visible to users. 32 characters max.')); ?>

			<?php echo $form->field($model, 'description')->textInput()->hint(Yii::t('Oauth/Application', 'The description will be visible to users. Between 10 and 256 characters.')); ?>

			<?php echo $form->field($model, 'website')->textInput()->hint(Yii::t('Oauth/Application', 'Fully qualified url to your website. The website will be displayed next to the description.')); ?>

			<?php echo $form->field($model, 'callback')->textInput()->hint(Yii::t('Oauth/Application', 'Callback where the user should be redirected after authenticating.')); ?>

			<div class="form-group">
				<?php echo Html::submitButton(Yii::t('Oauth/Application', 'Create application'), ['class' => 'btn btn-primary', 'name' => 'create-button']) ?>
			</div>

		<?php ActiveForm::end(); ?>
	</div>
</div>

<?php echo $this->render('/developer/_footer');
