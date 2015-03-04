<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title[] = Yii::t('Oauth/Application', 'Applications');
$this->title[] = Yii::t('Developer', 'Developer');
?>

<?php echo $this->render('/developer/_header', [
	'active' => 'oauth-application/index',
]); ?>


<h1><?php echo Yii::t('Oauth/Application', 'Your applications'); ?> <small><a href="<?php echo Url::toRoute(['oauth-application/create']); ?>"><?php echo Yii::t('Oauth/Application', 'Create application') ?></a></small></h1>

<div id="oauth-consumer-index">
	<?php if (!empty($applications)): ?>
		<ul id="oauth-consumer-list" class="list-unstyled">
			<?php foreach ($applications as $application): ?>
				<li>
					<h3><a href="<?php echo Url::toRoute(['view', 'id' => $application->id]); ?>"><?php echo Html::encode($application->name); ?></a></h3>
					<p class="text-muted"><?php echo Html::encode($application->description); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<div class="alert alert-info">
			<p><?php echo Yii::t('Oauth/Application', 'You did not create a application yet.'); ?></p>
		</div>

		<p><a href="<?php echo Url::toRoute(['create']); ?>" clas="btn btn-primary"><?php echo Yii::t('Oauth/Application', 'Create application'); ?></a></p>
	<?php endif; ?>
</div>

<?php echo $this->render('/developer/_footer');
