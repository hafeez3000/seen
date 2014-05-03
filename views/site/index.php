<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;

?>
<div id="frontpage">

	<div class="jumbotron">
		<div class="row">
			<div class="col-md-6">
				<h1><?php echo Yii::t('Site/Index', 'Welcome to SEEN'); ?></h1>

				<p class="lead"><?php echo Yii::t('Site/Index', 'SEEN helps you to remember the Movies, TV Shows and episodes you\'ve already SEEN and to discover new great shows!'); ?></p>

				<p><a class="btn btn-lg btn-success" href="<?php echo Url::toRoute('/site/sign-up'); ?>" title="Sign Up">Sign up for FREE</a></p>
			</div>

			<div class="col-md-6">
				<?php echo Yii::$app->controller->randomImage(); ?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">
			<h2><?php echo Yii::t('Site/Index', 'Always Up To Date'); ?></h2>

			<p>We work with the API from <a href="https://www.themoviedb.org/">The Movie Database</a> so wen can provide information for almost every tv show and movie. If something is missing you can register on their platform and provide the missing information.</p>

			<p><a class="btn btn-default" href="<?php echo Url::toRoute(['tv']); ?>"><?php echo Yii::t('Site/Index', 'Browse TV Shows'); ?></a></p>
		</div>
		<div class="col-md-4">
			<h2><?php echo Yii::t('Site/Index', 'Easy to use'); ?></h2>

			<p>All you have to do is <code>subscribe</code> to a TV show and check the episodes you've already seen, the movies you do not even need to subscribe. You can also watch an episode or a movie multiple times.</p>

			<p><a class="btn btn-default" href="<?php echo Url::toRoute(['movie/index']); ?>"><?php echo Yii::t('Site/Index', 'Browse Movies'); ?></a></p>
		</div>
		<div class="col-md-4">
			<h2><?php echo Yii::t('Site/Index', 'API'); ?></h2>

			<p>We provide an API so you can create great apps to enhance the system or create native clients for mobile devices. With the methods you can access and modify all of the data we have access to, too.</p>

			<p><a class="btn btn-default" href="<?php echo Url::toRoute(['developer/index']); ?>"><?php echo Yii::t('Site/Index', 'Developer Docs'); ?></a></p>
		</div>
	</div>
</div>
