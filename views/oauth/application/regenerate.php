<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;
use \yii\helpers\Html;
use \yii\widgets\ActiveForm;

$this->title[] = Yii::t('Oauth/Application/Regenerate', 'Regenerate {name}', ['name' => $model->name]);
$this->title[] = Yii::t('Developer', 'Developer');
?>

<?php echo $this->render('/developer/_header', [
	'active' => 'oauth-application/regenerate',
]); ?>


<h1><?php echo Html::encode($model->name); ?></h1>

<div id="oauth-consumer-regenerate">
	<?php $form = ActiveForm::begin([
		'id' => 'oauth-application-regenerate',
	]); ?>
		<div class="alert alert-info">
			<?php echo Yii::t('Oauth/Application/Regenerate', 'Are you sure you want to regenerate the key and secret? The old key and secret will become invalid.'); ?>
		</div>

		<p>
			<button type="submit" name="regenerate" href="<?php echo Url::toRoute(['']) ?>" class="btn btn-primary"><?php echo Yii::t('Oauth/Application/Regenerate', 'Regenerate'); ?></button>
			<a href="<?php echo Url::toRoute(['view', 'id' => $model->id]); ?>" class="btn btn-default"><?php echo Yii::t('Oauth/Application/Regenerate', 'Abort'); ?></a>
		</p>
	<?php ActiveForm::end(); ?>
</div>

<?php echo $this->render('/developer/_footer');
