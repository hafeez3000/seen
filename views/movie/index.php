<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\widgets\ActiveForm;

$this->title[] = Yii::t('Movie/Index', 'Movies');
?>

<div id="movie-index">
    <div class="row" id="movie-dashboard-header">
        <div class="col-sm-6 col-md-8">
            <h1><?php echo Yii::t('Movie/Index', 'Popular Movies'); ?></h1>
        </div>

        <div class="col-sm-6 col-md-4">
            <?php $form = ActiveForm::begin([
                'action' => Yii::$app->urlManager->createAbsoluteUrl(['movie/load']),
            ]); ?>
                <input type="hidden" id="movie-search" name="id" style="margin-top: 30px; width: 100%;">
            <?php ActiveForm::end(); ?>
        </div>
    </div>

	<div class="row">
		<?php foreach ($movies as $movie): ?>
			<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 movie-index">
				<a href="<?php echo Url::toRoute(['view', 'slug' => $movie->slug]); ?>" title="<?php echo Html::encode($movie->title); ?>">
					<img <?php echo $movie->posterUrlLarge; ?>>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>
