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
		<?php echo nl2br(Html::encode($message)) ?>
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
