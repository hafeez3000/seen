<?php
/**
 * @var yii\web\View $this
 */

use \yii\bootstrap\Nav;
?>

<div id="developer">
	<div class="row">
		<div class="col-md-3 col-lg-2">
			<div id="developer-sidebar">
				<?php echo Nav::widget([
					'items' => [
						[
							'label' => Yii::t('Developer', 'Overview'),
							'url' => ['developer/index'],
							'active' => ($active == 'developer/index'),
						],
						[
							'label' => Yii::t('Oauth/Application', 'Applications'),
							'url' => ['oauth-application/index'],
							'active' => (strpos($active, 'oauth-application') !== false),
						],
					],
					'options' => [
						'class' => 'nav nav-pills nav-stacked',
					]
				]); ?>
			</div>
		</div>

		<div class="col-md-9 col-lg-10">
