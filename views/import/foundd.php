<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;
use \yii\helpers\Html;

use \app\components\ActiveForm;
use \app\models\forms\ImportForm;

$this->title[] = Yii::t('Import/Foundd', 'Import data from FOUNDD');
?>
<div id="import-foundd" class="import-data">
	<h1><?php echo Yii::t('Import/Foundd', 'Import data from FOUNDD'); ?></h1>

	<div class="progress" id="import-progress">
		<div class="progress-bar" style="width: 0%;"><span class="import-current">0</span>/<span class="import-max">0</span></div>
	</div>

	<?php foreach ($movies as $movie): ?>
		<div class="import-object import-movie import-foundd" data-title="<?php echo Html::encode($movie->t); ?>">
			<h2><?php echo Html::encode($movie->t); ?> <small><a data-skip="true" title="<?php echo Yii::t('Import/Foundd', 'Skip movie'); ?>"><span class="glyphicon glyphicon-step-forward"></span></a></small></h2>
		</div>
	<?php endforeach; ?>

	<p>
		<a href="<?php echo Url::toRoute(['movie/index']); ?>" class="btn btn-primary"><?php echo Yii::t('Import/Foundd', 'Back to your movies'); ?></a>
	</p>
</div>
