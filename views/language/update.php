<?php
/**
 * @var yii\web\View $this
 * @var app\models\Language $model
 */

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\ActiveForm;

$this->title[] = Yii::t('Language', 'Update');
$this->title[] = !empty($model->en_name) ? $model->en_name : $model->iso;
$this->title[] = Yii::t('Language', 'Languages');
?>
<div id="account">
	<h1><?php echo Yii::t('Language', 'Update {name}', ['name' => !empty($model->en_name) ? $model->en_name : $model->iso]); ?></h1>

	<div class="row">
		<div class="col-md-6">
			<?php $form = ActiveForm::begin([
				'id' => 'language-form',
			]); ?>

			<?php echo $form->field($model, 'name'); ?>

			<?php echo $form->field($model, 'en_name'); ?>

			<?php echo $form->field($model, 'rtl')->checkBox(); ?>

			<?php echo $form->field($model, 'hide')->checkBox(); ?>

			<div class="form-group">
				<?php echo Html::submitButton(Yii::t('Language', 'Save Language'), ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
			</div>

			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
