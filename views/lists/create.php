<?php
/**
 * @var $this yii\web\View
 * @var $model app\models\Lists
 */

$this->title[] = Yii::t('Lists/Create', 'Create new list');
?>
<div id="lists-create">
	<h1><?php echo Yii::t('Lists/Create', 'Create new list'); ?></h1>

	<div class="row">
		<div class="col-md-6">
			<?php echo $this->render('_form', [
				'model' => $model,
			]); ?>
		</div>
	</div>
</div>
