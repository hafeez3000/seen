<?php
/**
 * @var yii\web\View $this
 */

use \yii\widgets\LinkPager;

echo $this->render('_navigation');

echo LinkPager::widget([
	'pagination' => $pages,
]);

?>

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
