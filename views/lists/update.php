<?php
/**
 * @var $this yii\web\View
 * @var $model app\models\Lists
 */

use yii\helpers\Html;

$this->title = Yii::t('Lists', 'Update {list}', [
	'list' => $model->title,
]);
?>
<div class="lists-update">

	<h1><?php echo Html::encode($this->title) ?></h1>

	<div class="row">
		<div class="col-md-6">
			<?php echo $this->render('_form', [
				'model' => $model,
			]); ?>
		</div>
	</div>
</div>
