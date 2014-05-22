<?php
/**
 * @var yii\web\View $this
 * @var string $name
 * @var string $message
 * @var Exception $exception
 */

use yii\helpers\Html;

$statusCode = isset($exception->statusCode) ? $exception->statusCode : Yii::t('Error', 'Unknown');

$this->title[] = Yii::t('Error', 'Error {code}', ['code' => $statusCode]);
?>
<div id="error">
	<h1><?php echo Html::encode(Yii::t('Error', 'Error {code}', ['code' => $statusCode])); ?></h1>

	<div class="alert alert-danger">
		<?php if ((defined('YII_DEBUG') && YII_DEBUG) || Yii::$app->user->can('viewLogs')): ?>
            <?php echo nl2br(Html::encode($message)) ?>
		<?php else: ?>
			<?php if ($statusCode == 404): ?>
               <?php echo Yii::t('Error', 'The site you were looking for does not exist! Please contact us if you think this was our mistake.') ?>
           <?php else: ?>
               <?php echo Yii::t('Error', 'There was an error while processing your request! We got informed and are trying to fix this error as soon as possible.') ?>
           <?php endif; ?>
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
    _paq.push(['setCustomVariable',
        1,
        "Error",
        "<?php echo $statusCode; ?> - <?php echo Html::encode($message); ?>",
        "page"
    ]);
</script>
