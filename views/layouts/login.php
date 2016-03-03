<?php
/**
 * @var \yii\web\View $this
 * @var string $content
 */

use yii\helpers\Url;
use yii\helpers\Html;

use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php
	if (is_array($this->title))
		echo Html::encode(implode(' - ', $this->title) . ' - ' . Yii::$app->name);
	elseif (!empty($this->title))
		echo Html::encode($this->title);
	else
		echo Html::encode(Yii::$app->name);
	?></title>

    <?php echo $this->render('//layouts/js_head.php'); ?>

	<?php $this->head() ?>
</head>
	<body>
		<?php $this->beginBody() ?>

		<div id="login-container">
			<?php echo $this->render('//layouts/flash.php'); ?>

			<?php echo $content; ?>
		</div>

		<div id="login-back">
			<a href="<?php echo Url::toRoute(['/']) ?>"><?php echo Yii::t('Login', 'Back to SEEN'); ?></a>
		</div>

        <?php echo $this->render('//layouts/js_foot.php'); ?>

		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>
