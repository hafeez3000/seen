<?php
/**
 * @var \yii\web\View $this
 * @var string $content
 */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\bootstrap\Alert;

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

	<script type="text/javascript">
		var App = {
			language: "<?php echo Yii::$app->language; ?>",
			themoviedb: {
				key: "<?php echo Yii::$app->params['themoviedb']['key']; ?>",
				url: "<?php echo Yii::$app->params['themoviedb']['url']; ?>",
				image_url: "<?php echo Yii::$app->params['themoviedb']['image_url']; ?>"
			},
			translation: {
				noPosterImage: "<?php echo Yii::t('Show', 'No image available'); ?>"
			}
		}
	</script>

	<?php $this->head() ?>
</head>
	<body>
		<?php $this->beginBody() ?>

		<div id="login-container">
			<div id="flash-messages">
				<?php
					$flashMessages = Yii::$app->session->getAllFlashes();

					if (is_array($flashMessages)) {
						foreach ($flashMessages as $key => $message) {
							if ($key == 'error')
								$key = 'danger';

							echo Alert::widget([
								'options' => [
									'class' => 'alert-' . $key
								],
								'body' => $message,
							]);
						}
					}
				?>
			</div>

			<?php echo $content; ?>
		</div>

		<div id="login-back">
			<a href="<?php echo Url::toRoute(['/']) ?>"><?php echo Yii::t('Login', 'Back to SEEN'); ?></a>
		</div>

		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>
