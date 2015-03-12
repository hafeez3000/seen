<?php
/**
 * @var $this yii\web\View
 * @var $model app\models\Lists
 * @var $form yii\widgets\ActiveForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div id="lists-form">

	<?php $form = ActiveForm::begin(); ?>

	<?php echo $form->field($model, 'title')->textInput(['maxlength' => 255]); ?>

	<?php echo $form->field($model, 'description')->textarea(['rows' => 6]); ?>

	<?php echo $form->field($model, 'public')->checkbox(); ?>

	<?php if (Yii::$app->user->can('admin')): ?>
		<?php echo $form->field($model, 'highlighted')->checkbox(); ?>
	<?php endif; ?>

	<div class="form-group">
		<?php echo Html::submitButton($model->isNewRecord ? Yii::t('Lists', 'Create') : Yii::t('Lists', 'Update'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
