<?php
/**
 * @var yii\web\View $this
 */

use \app\components\LanguageHelper;
use \yii\helpers\Html;
?>

<tr id="log-<?php echo $log->id; ?>" class="log-message <?php echo $log->class; ?>">
	<td><?php echo Yii::t('Log/View', '#{id}', ['id' => $log->id]); ?></td>
	<td><?php echo LanguageHelper::dateTime($log->log_time); ?></td>
	<td><?php echo Html::encode($log->category); ?></td>
	<td><pre><?php echo Html::encode($log->message); ?></pre></td>
</tr>
