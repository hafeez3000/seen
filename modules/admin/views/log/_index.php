<?php
/**
 * @var yii\web\View $this
 */

use \yii\widgets\LinkPager;
use \yii\helpers\Url;
?>

<p>
	<div class="btn-group">
		<a href="<?php echo Url::toRoute(['index']) ?>" class="btn <?php echo (Yii::$app->controller->action->id == 'index') ? 'btn-primary' : 'btn-default'; ?>"><?php echo Yii::t('Log', 'All') ?></a>
		<a href="<?php echo Url::toRoute(['important']) ?>" class="btn <?php echo (Yii::$app->controller->action->id == 'important') ? 'btn-primary' : 'btn-default'; ?>"><?php echo Yii::t('Log', 'Important') ?></a>
		<a href="<?php echo Url::toRoute(['missing']) ?>" class="btn <?php echo (Yii::$app->controller->action->id == 'missing') ? 'btn-primary' : 'btn-default'; ?>"><?php echo Yii::t('Log', '404') ?></a>
	</div>
</p>

<?php echo LinkPager::widget([
	'pagination' => $pages,
]); ?>

<table id="log-table" class="table table-condensed">
	<thead>
		<tr>
			<th><?php echo Yii::t('Log', 'ID'); ?></th>
			<th><?php echo Yii::t('Log', 'Time'); ?></th>
			<th><?php echo Yii::t('Log', 'Category'); ?></th>
			<th><?php echo Yii::t('Log', 'Message'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($logs as $log): ?>
			<?php echo $this->render('_view', [
				'log' => $log,
			]) ?>
		<?php endforeach; ?>
	</tbody>
</table>

<?php echo LinkPager::widget([
	'pagination' => $pages,
]); ?>
