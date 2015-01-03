<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;
?>

<p>
	<div class="btn-group">
		<a href="<?php echo Url::toRoute(['index']) ?>" class="btn <?php echo (Yii::$app->controller->action->id == 'index') ? 'btn-primary' : 'btn-default'; ?>"><?php echo Yii::t('Log', 'All') ?></a>
		<a href="<?php echo Url::toRoute(['important']) ?>" class="btn <?php echo (Yii::$app->controller->action->id == 'important') ? 'btn-primary' : 'btn-default'; ?>"><?php echo Yii::t('Log', 'Important') ?></a>
		<a href="<?php echo Url::toRoute(['missing']) ?>" class="btn <?php echo (Yii::$app->controller->action->id == 'missing') ? 'btn-primary' : 'btn-default'; ?>"><?php echo Yii::t('Log', '404') ?></a>
		<a href="<?php echo Url::toRoute(['truncate']) ?>" class="btn <?php echo (Yii::$app->controller->action->id == 'truncate') ? 'btn-primary' : 'btn-default'; ?>"><?php echo Yii::t('Log', 'Truncate') ?></a>
	</div>
</p>
