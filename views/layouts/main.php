<?php
/**
 * @var \yii\web\View $this
 * @var string $content
 */

use \yii\helpers\Html;
use \yii\bootstrap\Nav;
use \yii\bootstrap\NavBar;

use \app\assets\AppAsset;
use \app\components\LanguageHelper;

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

	<?php echo Html::csrfMetaTags(); ?>

	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
	<link type="image/x-icon" href="/favicon.ico" />

	<?php $this->head() ?>
</head>
	<body>
		<?php $this->beginBody() ?>

		<?php echo $this->render('//layouts/_spinner.php'); ?>

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
						[
								'label' => Yii::t('Site/Navigation', 'Admin'),
								'url' => ['/admin'],
								'visible' => Yii::$app->user->can('supporter') || Yii::$app->user->can('admin'),
						],
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
							LanguageHelper::navigation(),
						],
					]);
				} else {
					echo Nav::widget([
						'options' => ['class' => 'navbar-nav navbar-right'],
						'items' => [
							['label' => Yii::t('Site/Navigation', 'TV Shows'), 'url' => ['tv/index'], 'active' => Yii::$app->controller->id == 'tv'],
							['label' => Yii::t('Site/Navigation', 'Movies'), 'url' => ['movie/index'], 'active' => Yii::$app->controller->id == 'movie'],
							//['label' => Yii::t('Site/Navigation', 'Lists'), 'url' => ['lists/index'], 'active' => Yii::$app->controller->id == 'lists'],
							['label' => Yii::$app->user->identity->email,
								'active' => Yii::$app->controller->id == 'user',
								'url' => '#',
								'items' => [
									['label' => Yii::t('Site/Navigation', 'Account'), 'url' => ['user/account']],
									['label' => Yii::t('Site/Navigation', 'Import'), 'url' => ['user/import']],
									['label' => '', 'options' => ['class' => 'divider']],
									['label' => Yii::t('Site/Navigation', 'Logout'), 'url' => ['site/logout']],
								]
							],
							LanguageHelper::navigation(),
						],
					]);
				}

				NavBar::end();
			?>
		</header>

		<div id="content" class="container">
			<?php echo $this->render('//layouts/flash.php'); ?>

			<?php echo $this->render('/site/_search'); ?>

			<?php echo $content; ?>
		</div>

		<footer>
			<div class="container">
				<div class="clearfix">
					<div class="pull-left">
						<p>Copyright VisualAppeal, 2012-<?php echo date('Y') ?></p>
					</div>

					<div class="pull-right">
						<p>
							<?php echo Html::a(Yii::t('Site/Navigation', 'Imprint'), ['site/imprint']); ?> |
							<?php echo Html::a(Yii::t('Site/Navigation', 'Privacy'), ['site/privacy']); ?> |
							<?php echo Html::a(Yii::t('Site/Navigation', 'Developer'), ['developer/index']); ?>
						</p>
					</div>
				</div>

				<div class="clearfix">
					<a href="https://mixpanel.com/f/partner"><img src="//cdn.mxpnl.com/site_media/images/partner/badge_blue.png" alt="Mobile Analytics" /></a>
					<a href="https://twitter.com/seenappcom" class="twitter-follow-button" data-show-count="false" data-lang="<?php echo Yii::$app->language; ?>">Follow @seenappcom</a>
				</div>
			</div>
		</footer>

		<?php echo $this->render('//layouts/js_foot.php'); ?>

		<!-- Twitter -->
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>
