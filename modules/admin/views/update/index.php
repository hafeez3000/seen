<?php

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title[] = Yii::t('Update', 'Outstanding Updates');
?>

<h1><?php echo Yii::t('Update', 'Outstanding Updates'); ?></h1>

<div id="updates-index">
	<h2><?php echo Yii::t('Update', 'Active Cronjobs'); ?></h2>

	<?php if (count($cronjobs)): ?>
		<table class="table table-bordered table-condensed table-striped">
			<thead>
				<tr>
					<th><?php echo Yii::t('Update', 'Hour'); ?></th>
					<th><?php echo Yii::t('Update', 'Minute'); ?></th>
					<th><?php echo Yii::t('Update', 'Command'); ?></th>
					<th><?php echo Yii::t('Update', 'Outstanding updates'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cronjobs as $cronjob): ?>
					<tr data-url="<?php echo Url::toRoute(['count', 'command' => $cronjob['command']]); ?>">
						<td><?php echo $cronjob['hour']; ?></td>
						<td><?php echo $cronjob['minute']; ?></td>
						<td><?php echo $cronjob['command']; ?></td>
						<td class="update-count"></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<?php echo Alert::widget([
			'options' => [
				'class' => 'alert-info',
			],
			'body' => Yii::t('Update', 'Currently there are no active cronjobs!'),
		]); ?>
	<?php endif; ?>
				<tr>
					<td><?php echo $cronjob['hour']; ?></td>
					<td><?php echo $cronjob['minute']; ?></td>
					<td><?php echo $cronjob['command']; ?></td>
					<td><?php echo number_format($cronjob['updates'], 0, ',', '.'); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
