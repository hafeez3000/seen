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
	<?php echo Yii::t('Developer/Overview', 'SEEN uses <a href="{oauthUrl}">OAuth2</a> to authorize the client. Therefore you have to <a href="{consumerUrl}">register an application</a> to obtain your key and secret.', [
		'oauthUrl' => 'http://oauth.net/2/',
		'consumerUrl' => Url::toRoute(['oauth-application/create']),
	]); ?>
</p>

<p>
	<?php echo Yii::t('Developer/Overview', 'The documentation is available at <a href="{url}">apiary</a>. With apiary you are able to use a proxy server to record and inspect your requests. The proxy server is only for development purposes. For production environments please use the real url.', [
			'url' => 'http://docs.seen.apiary.io/',
	]); ?>
</p>

<p>
	<a href="http://docs.seen.apiary.io/" class="btn btn-primary"><?php echo Yii::t('Developer/Overview', 'Api Documentation'); ?></a>
</p>

<?php echo $this->render('_footer');