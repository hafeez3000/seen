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
			baseUrl: "<?php echo Yii::$app->request->baseUrl; ?>",
			language: "<?php echo Yii::$app->language; ?>",
			themoviedb: {
				key: "<?php echo Yii::$app->params['themoviedb']['key']; ?>",
				url: "<?php echo Yii::$app->params['themoviedb']['url']; ?>",
				image_url: "<?php echo Yii::$app->params['themoviedb']['image_url']; ?>"
			},
			translation: {
				unknown_error: "<?php echo Yii::t('Error', 'An unknown error occured! Please try again later.'); ?>",
				first_aired: "<?php echo Yii::t('Show', 'First aired'); ?>",
				released: "<?php echo Yii::t('Movie', 'Released'); ?>",
				votes: "<?php echo Yii::t('Show', 'Votes'); ?>"
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
						['label' => Yii::t('Site/Navigation', 'Contact'), 'url' => ['site/contact']],
					],
				]);

				if (Yii::$app->user->isGuest) {
					echo Nav::widget([
						'options' => ['class' => 'navbar-nav navbar-right'],
						'items' => [
							['label' => Yii::t('Site/Navigation', 'TV Shows'), 'url' => ['tv/index'], 'active' => Yii::$app->controller->id == 'tv'],
							['label' => Yii::t('Site/Navigation', 'Movies'), 'url' => ['movie/index'], 'active' => Yii::$app->controller->id == 'movie'],
							['label' => Yii::t('Site/Navigation', 'Login'), 'url' => ['site/login']],
							['label' => Yii::t('Site/Navigation', 'Sign Up'), 'url' => ['site/sign-up']],
						],
					]);
				} else {
					echo Nav::widget([
						'options' => ['class' => 'navbar-nav navbar-right'],
						'items' => [
							['label' => Yii::t('Site/Navigation', 'TV Shows'), 'url' => ['tv/index'], 'active' => Yii::$app->controller->id == 'tv'],
							['label' => Yii::t('Site/Navigation', 'Movies'), 'url' => ['movie/index'], 'active' => Yii::$app->controller->id == 'movie'],
							['label' => Yii::$app->user->identity->email,
								'active' => Yii::$app->controller->id == 'user',
								'items' => [
									['label' => Yii::t('Site/Navigation', 'Account'), 'url' => ['user/account']],
									['label' => Yii::t('Site/Navigation', 'Import'), 'url' => ['user/import']],
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

		<?php echo $this->render('/layouts/social.php'); ?>
	</body>
</html>
<?php $this->endPage() ?>
