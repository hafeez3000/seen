<?php

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title[] = Yii::t('Admin', 'Admin Dashboard');
?>

<h1><?php echo Yii::t('Admin', 'Admin Dashboard'); ?></h1>

<div id="admin-dashboard" class="row">
	<?php if (Yii::$app->user->can('viewEmails')): ?>
		<div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
			<a href="<?php echo Url::toRoute('email/index'); ?>" title="<?php echo Yii::t('Admin', 'Emails'); ?>">
				<img src="<?php echo Yii::$app->request->baseUrl ?>/images/admin/mail.png" alt="<?php echo Yii::t('Admin', 'Emails'); ?>">
			</a>
		</div>
	<?php endif; ?>
	<?php if (Yii::$app->user->can('viewLogs')): ?>
		<div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
			<a href="<?php echo Url::toRoute('log/index'); ?>" title="<?php echo Yii::t('Admin', 'Log Messages'); ?>">
				<img src="<?php echo Yii::$app->request->baseUrl ?>/images/admin/log.png" alt="<?php echo Yii::t('Admin', 'Log Messages'); ?>">
			</a>
		</div>
	<?php endif; ?>
	<?php if (Yii::$app->user->can('manageLanguages')): ?>
		<div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
			<a href="<?php echo Url::toRoute('language/index'); ?>" title="<?php echo Yii::t('Admin', 'Languages'); ?>">
				<img src="<?php echo Yii::$app->request->baseUrl ?>/images/admin/languages.png" alt="<?php echo Yii::t('Admin', 'Languages'); ?>">
			</a>
		</div>
	<?php endif; ?>
	<?php if (Yii::$app->user->can('viewUsers')): ?>
		<div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
			<a href="<?php echo Url::toRoute('user/index'); ?>" title="<?php echo Yii::t('Admin', 'Users'); ?>">
				<img src="<?php echo Yii::$app->request->baseUrl ?>/images/admin/users.png" alt="<?php echo Yii::t('Admin', 'Users'); ?>">
			</a>
		</div>
	<?php endif; ?>
	<?php if (Yii::$app->user->can('viewUpdates')): ?>
		<div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
			<a href="<?php echo Url::toRoute('update/index'); ?>" title="<?php echo Yii::t('Admin', 'Updates'); ?>">
				<img src="<?php echo Yii::$app->request->baseUrl ?>/images/admin/updates.png" alt="<?php echo Yii::t('Admin', 'Updates'); ?>">
			</a>
		</div>
	<?php endif; ?>
	<?php if (Yii::$app->user->can('admin')): ?>
		<div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
			<a href="<?php echo Url::toRoute('authorization/index'); ?>" title="<?php echo Yii::t('Admin', 'Authorization'); ?>">
				<img src="<?php echo Yii::$app->request->baseUrl ?>/images/admin/auth.png" alt="<?php echo Yii::t('Admin', 'Authorization'); ?>">
			</a>
		</div>
	<?php endif; ?>
</div>
