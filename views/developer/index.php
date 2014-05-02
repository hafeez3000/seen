<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;

$this->title[] = Yii::t('Developer', 'Developer');
?>

<?php echo $this->render('_header', [
	'active' => 'developer/index',
]); ?>


<h1><?php echo Yii::t('Developer/Overview', 'Overview'); ?></h1>

<p>
	<?php echo Yii::t('Developer/Overview', 'SEEN uses <a href="{oauthUrl}">OAuth2</a> to authorize the client. Therefore you have to <a href="{consumerUrl}">register a application</a> to obtain your key and secret.', [
		'oauthUrl' => 'http://oauth.net/2/',
		'consumerUrl' => Url::toRoute(['oauthApplication/create']),
	]); ?>
</p>

<?php echo $this->render('_footer');