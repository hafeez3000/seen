<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;

$this->title[] = Yii::t('Developer', 'Consumers');
$this->title[] = Yii::t('Developer', 'Developer');
?>

<?php echo $this->render('/developer/_header', [
	'active' => 'oauthApplication/index',
]); ?>


<h1><?php echo Yii::t('Oauth/Application', 'Your applications'); ?> <small><a href="<?php echo Url::toRoute(['oauth-application/create']); ?>"><?php echo Yii::t('Oauth/Application', 'Create consumer') ?></a></small></h1>

<?php if (!empty($applications)): ?>
<?php else: ?>
	<div class="alert alert-info">
		<p><?php echo Yii::t('Oauth/Application', 'You did not create a consumer application yet.'); ?></p>
	</div>
<?php endif; ?>

<?php echo $this->render('/developer/_footer');