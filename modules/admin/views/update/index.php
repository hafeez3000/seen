<?php

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title[] = Yii::t('Update', 'Outstanding Updates');
?>

<h1><?php echo Yii::t('Update', 'Outstanding Updates'); ?></h1>

<div id="updates-index">
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
