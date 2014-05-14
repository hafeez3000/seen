<?php

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title[] = Yii::t('Admin', 'Admin Dashboard');
?>

<h1><?php echo Yii::t('Admin', 'Admin Dashboard'); ?></h1>

<div id="admin-dashboard" class="row">
	<div class="col-lg-3 col-md-4">
		<?php if (Yii::$app->user->can('viewEmails')): ?>
			<a href="<?php echo Url::toRoute('email/index'); ?>" title="<?php echo Yii::t('Admin', 'Emails'); ?>">
				<img src="<?php echo Yii::$app->request->baseUrl ?>/images/admin/mail.png" alt="<?php echo Yii::t('Admin', 'Emails'); ?>">
			</a>
		<?php endif; ?>
	</div>
	<div class="col-lg-3 col-md-4">
		<?php if (Yii::$app->user->can('viewLogs')): ?>
			<a href="<?php echo Url::toRoute('log/index'); ?>" title="<?php echo Yii::t('Admin', 'Log Messages'); ?>">
				<img src="<?php echo Yii::$app->request->baseUrl ?>/images/admin/log.png" alt="<?php echo Yii::t('Admin', 'Log Messages'); ?>">
			</a>
		<?php endif; ?>
	</div>
	<div class="col-lg-3 col-md-4">
		<?php if (Yii::$app->user->can('manageLanguages')): ?>
			<a href="<?php echo Url::toRoute('language/index'); ?>" title="<?php echo Yii::t('Admin', 'Languages'); ?>">
				<img src="<?php echo Yii::$app->request->baseUrl ?>/images/admin/languages.png" alt="<?php echo Yii::t('Admin', 'Languages'); ?>">
			</a>
		<?php endif; ?>
	</div>
	<div class="col-lg-3 col-md-4">
		<?php if (Yii::$app->user->can('manageUsers')): ?>
			<a href="<?php echo Url::toRoute('user/index'); ?>" title="<?php echo Yii::t('Admin', 'Users'); ?>">
				<img src="<?php echo Yii::$app->request->baseUrl ?>/images/admin/users.png" alt="<?php echo Yii::t('Admin', 'Users'); ?>">
			</a>
		<?php endif; ?>
	</div>
	<div class="col-lg-3 col-md-4">
		<?php if (Yii::$app->user->can('viewUpdates')): ?>
			<a href="<?php echo Url::toRoute('update/index'); ?>" title="<?php echo Yii::t('Admin', 'Updates'); ?>">
				<img src="<?php echo Yii::$app->request->baseUrl ?>/images/admin/updates.png" alt="<?php echo Yii::t('Admin', 'Updates'); ?>">
			</a>
		<?php endif; ?>
	</div>
</div>
