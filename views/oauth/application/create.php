<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;

$this->title[] = Yii::t('Developer', 'Consumers');
$this->title[] = Yii::t('Developer', 'Developer');
?>

<?php echo $this->render('/developer/_header', [
	'active' => 'oauthApplication/create',
]); ?>


<h1><?php echo Yii::t('Oauth/Application', 'Create application'); ?></h1>



<?php echo $this->render('/developer/_footer');