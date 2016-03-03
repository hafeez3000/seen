<?php
/**
 * @var $this yii\web\View
 * @var $model app\models\Lists
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \yii\widgets\ActiveForm;

$this->title[] = Yii::t('ListsEntry/Create', 'Add new item to {list}', [
	'list' => $list->title,
]);
?>
<div id="lists-entry-create">
	<h1><?php echo Yii::t('ListsEntry/Create', 'Add new item to {list}', ['list' => $list->title]); ?></h1>

	<div class="row">
		<div class="col-md-6" id="lists-entry-form">

			<?php $form = ActiveForm::begin(); ?>

			<?php echo Html::activeHiddenInput($model, 'type'); ?>

			<?php echo $form->field($model, 'themoviedb_id')->textInput(['class' => 'search']); ?>

			<?php echo $form->field($model, 'description')->textarea(['rows' => 6]); ?>

			<?php echo $form->field($model, 'position')->dropDownList(ArrayHelper::map($list->entries, 'position', 'title'), [
				'prompt' => Yii::t('ListsEntry', 'Insert at the beginning'),
			]); ?>

			<div class="form-group">
				<a href="<?php echo Yii::$app->urlManager->createUrl(['/lists/view', 'slug' => $list->slug]); ?>" class="btn btn-default"><?php echo Yii::t('ListsEntry', 'Cancel'); ?></a>
				<?php echo Html::submitButton($model->isNewRecord ? Yii::t('ListsEntry', 'Create') : Yii::t('ListsEntry', 'Update'), ['class' => 'btn btn-primary']) ?>
			</div>

			<?php ActiveForm::end(); ?>

		</div>

	</div>
</div>
