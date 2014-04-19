<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;

?>
<div id="frontpage">

	<div class="container">
		<div class="jumbotron">
			<div class="row">
				<div class="col-md-6">
					<h1><?php echo Yii::t('Site/Index', 'Welcome to SEEN'); ?></h1>

					<p class="lead"><?php echo Yii::t('Site/Index', 'SEEN helps you to remember the Movies, TV Shows and episodes you\'ve already SEEN and to discover new great shows!'); ?></p>

					<p><a class="btn btn-lg btn-success" href="<?php echo Url::toRoute('/site/register'); ?>" title="Sign Up">Sign up for FREE</a></p>
				</div>

				<div class="col-md-6">
					<img src="http://placehold.it/524x225">
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row">
			<div class="col-lg-4">
				<h2><?php echo Yii::t('Site/Index', 'Lots of data'); ?></h2>

				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
					dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
					ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
					fugiat nulla pariatur.</p>

				<p><a class="btn btn-default" href="#"><?php echo Yii::t('Site/Index', 'Browse'); ?></a></p>
			</div>
			<div class="col-lg-4">
				<h2><?php echo Yii::t('Site/Index', 'Easy to use'); ?></h2>

				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
					dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
					ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
					fugiat nulla pariatur.</p>

				<p><a class="btn btn-default" href="#"><?php echo Yii::t('Site/Index', 'Help'); ?></a></p>
			</div>
			<div class="col-lg-4">
				<h2><?php echo Yii::t('Site/Index', 'API'); ?></h2>

				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
					dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
					ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
					fugiat nulla pariatur.</p>

				<p><a class="btn btn-default" href="#"><?php echo Yii::t('Site/Index', 'Developer Docs'); ?></a></p>
			</div>
		</div>

	</div>
</div>
