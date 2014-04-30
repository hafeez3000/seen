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
							'label' => Yii::t('Oauth/Application', 'Consumer'),
							'url' => ['oauthApplication/index'],
							'active' => (strpos('oauthApplication', $active) >= 0),
							'items' => [
								[
									'label' => Yii::t('Oauth/Application', 'Create'),
									'url' => ['oauthApplication/index'],
									'active' => ($active == 'oauthApplication/create'),
								]
							]
						],
					],
					'options' => [
						'class' => 'nav nav-pills nav-stacked',
					]
				]); ?>
			</div>
		</div>

		<div class="col-md-9 col-lg-10">