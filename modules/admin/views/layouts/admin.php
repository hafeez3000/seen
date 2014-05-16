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

	<?php $this->head() ?>
</head>
	<body>
		<?php $this->beginBody() ?>

		<header>
			<?php
				NavBar::begin([
					'brandLabel' => Yii::t('Admin', '{name} - Administration', ['name' => Yii::$app->name]),
					'brandUrl' => ['/admin'],
					'options' => [
						'class' => 'navbar-inverse',
					],
				]);
				echo Nav::widget([
					'options' => ['class' => 'navbar-nav'],
					'items' => [
						[
							'label' => Yii::t('Site/Navigation', 'Email'),
							'url' => ['email/index'],
							'visible' => Yii::$app->user->can('viewEmails'),
							'active' => Yii::$app->controller->id == 'email',
						],
						[
							'label' => Yii::t('Site/Navigation', 'Log'),
							'url' => ['log/index'],
							'visible' => Yii::$app->user->can('viewLogs'),
							'active' => Yii::$app->controller->id == 'log',
						],
						[
							'label' => Yii::t('Site/Navigation', 'Language'),
							'url' => ['language/index'],
							'visible' => Yii::$app->user->can('manageLanguages'),
							'active' => Yii::$app->controller->id == 'language',
						],
                        [
                            'label' => Yii::t('Site/Navigation', 'Update'),
                            'url' => ['update/index'],
                            'visible' => Yii::$app->user->can('viewUpdates'),
                            'active' => Yii::$app->controller->id == 'update',
                        ],
					],
				]);

				echo Nav::widget([
					'options' => ['class' => 'navbar-nav navbar-right'],
					'items' => [
                        ['label' => Yii::t('Site/Navigation', 'Frontpage'), 'url' => Yii::$app->homeUrl],
						['label' => Yii::$app->user->identity->email,
							'active' => Yii::$app->controller->id == 'user',
							'items' => [
								['label' => Yii::t('Site/Navigation', 'Account'), 'url' => ['user/account']],
								['label' => Yii::t('Site/Navigation', 'Import'), 'url' => ['user/import']],
								['label' => '', 'options' => ['class' => 'divider']],
								['label' => Yii::t('Site/Navigation', 'Logout'), 'url' => ['site/logout']],
							]
						],
					],
				]);

				NavBar::end();
			?>
		</header>

		<div id="content" class="container">
			<?php echo $this->render('//layouts/flash.php'); ?>

			<div id="ajax-loading"></div>

			<?php echo $content; ?>
		</div>

		<footer>
			<div class="container">
				<p>Copyright VisualAppeal, 2012-<?php echo date('Y') ?></p>
				<p><?php echo Html::a(Yii::t('Site/Navigation', 'Imprint'), ['/site/imprint']); ?> | <?php echo Html::a(Yii::t('Site/Navigation', 'Privacy'), ['/site/privacy']); ?> | <?php echo Html::a(Yii::t('Site/Navigation', 'Developer'), ['/developer/index']); ?></p>
			</div>
		</footer>

        <?php echo $this->render('//layouts/js_foot.php'); ?>

		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>
