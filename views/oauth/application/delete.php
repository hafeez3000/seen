<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;
use \yii\helpers\Html;
use \yii\widgets\ActiveForm;

$this->title[] = Yii::t('Oauth/Application/Delete', 'Delete {name}', ['name' => $model->name]);
$this->title[] = Yii::t('Developer', 'Developer');
?>

<?php echo $this->render('/developer/_header', [
	'active' => 'oauth-application/delete',
]); ?>


<h1><?php echo Html::encode($model->name); ?></h1>

<div id="oauth-consumer-delete">
	<?php $form = ActiveForm::begin([
		'id' => 'oauth-application-delete',
	]); ?>
		<div class="alert alert-warning">
			<?php echo Yii::t('Oauth/Application/Delete', 'Are you sure you want to delete the application? Once the application is deleted, you are not able to restore it.'); ?>
		</div>

		<p>
			<button type="submit" name="delete" class="btn btn-danger"><?php echo Yii::t('Oauth/Application/Delete', 'Delete'); ?></button>
			<a href="<?php echo Url::toRoute(['view', 'id' => $model->id]); ?>" class="btn btn-default"><?php echo Yii::t('Oauth/Application/Delete', 'Abort'); ?></a>
		</p>
	<?php ActiveForm::end(); ?>
</div>

<?php echo $this->render('/developer/_footer');