<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;

use \app\components\ActiveForm;
use \app\models\forms\ImportForm;

$this->title[] = Yii::t('User/Import', 'Your Import');
?>
<div id="import">
	<h1><?php echo Yii::t('User/Import', 'Import Your Data'); ?></h1>

	<h2><?php echo Yii::t('User/Import', 'Foundd'); ?></h2>
	<p>
		<?php echo Yii::t('User/Import', 'Import your watched movies and subscribe to watched tv shows from <a href="{url}">FOUNDD</a>', ['url' => 'http://foundd.com/']); ?>
	</p>

	<p>
		<?php $form = ActiveForm::begin([
			'options' => [
				'enctype' => 'multipart/form-data'
			]
		]); ?>

			<?php echo $form->field($model, 'type', ['template' => '{input} {error}'])->hiddenInput(['value' => ImportForm::TYPE_FOUNDD]); ?>

			<?php echo $form->field($model, 'file', ['template' => '{input} {error}'])->fileInput(); ?>

			<?php echo Html::submitButton(Yii::t('User/Import', 'Upload'), ['class' => 'btn btn-primary']); ?>

		<?php ActiveForm::end(); ?>
	</p>
</div>
