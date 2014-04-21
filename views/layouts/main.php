<?php
/**
 * @var \yii\web\View $this
 * @var string $content
 */

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

		<header>
			<?php
				NavBar::begin([
					'brandLabel' => Yii::$app->name,
					'brandUrl' => Yii::$app->homeUrl,
					'options' => [
						'class' => 'navbar-inverse',
					],
				]);
				echo Nav::widget([
					'options' => ['class' => 'navbar-nav'],
					'items' => [
						['label' => Yii::t('Site/Navigation', 'Home'), 'url' => ['site/index']],
						['label' => Yii::t('Site/Navigation', 'About'), 'url' => ['site/about']],
						['label' => Yii::t('Site/Navigation', 'Contact'), 'url' => ['site/contact']],
					],
				]);

				if (Yii::$app->user->isGuest) {
					echo Nav::widget([
						'options' => ['class' => 'navbar-nav navbar-right'],
						'items' => [
								['label' => Yii::t('Site/Navigation', 'Login'), 'url' => ['site/login']],
								['label' => Yii::t('Site/Navigation', 'Sign Up'), 'url' => ['site/sign-up']],
						],
					]);
				} else {
					echo Nav::widget([
						'options' => ['class' => 'navbar-nav navbar-right'],
						'items' => [
							['label' => Yii::t('Site/Navigation', 'TV Shows'), 'url' => ['tv/index']],
							['label' => Yii::t('Site/Navigation', 'Movies'), 'url' => ['movie/index']],
							['label' => Yii::$app->user->identity->email,
								'items' => [
									['label' => Yii::t('Site/Navigation', 'Account'), 'url' => ['user/account']],
									['label' => '', 'options' => ['class' => 'divider']],
									['label' => Yii::t('Site/Navigation', 'Logout'), 'url' => ['site/logout'], 'linkOptions' => ['data-method' => 'post']],
								]
							]
						],
					]);
				}

				NavBar::end();
			?>
		</header>

		<div id="content" class="container">
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

			<div id="ajax-loading"></div>

			<?php echo $content; ?>
		</div>

		<footer>
			<div class="container">
				<p>Copyright VisualAppeal, 2012-<?php echo date('Y') ?></p>
				<p><?php echo Html::a(Yii::t('Site/Navigation', 'Imprint'), ['site/imprint']); ?> | <?php echo Html::a(Yii::t('Site/Navigation', 'Privacy'), ['site/privacy']); ?></p>
			</div>
		</footer>

		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>
