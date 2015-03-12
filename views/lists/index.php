<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\widgets\ListView;

$this->title[] = Yii::t('Lists', 'Lists');
?>
<div class="lists-index">

	<h1>
		<?php echo Yii::t('Lists', 'Lists of movies/persons/tv shows') ?>

		<?php if (!Yii::$app->user->isGuest): ?>
			<small>
				<a href="<?php echo Yii::$app->urlManager->createUrl(['lists/create']); ?>" title="<?php echo Yii::t('Lists', 'Create your own list'); ?>">
					<span class="glyphicon glyphicon-plus"></span>
				</a>
			</small>
		<?php endif; ?>
	</h1>

	<?php if (count($lists)): ?>
		<div class="row" id="lists-index">
			<?php foreach ($lists as $list): ?>
				<div class="col-md-6">
					<h3><a href="<?php echo Yii::$app->urlManager->createUrl(['/lists/view', 'slug' => $list->slug]) ?>"><?php echo Html::encode($list->title); ?></a></h3>

					<?php if (isset($list->lastEntry->id)): ?>
						<a href="<?php echo Yii::$app->urlManager->createUrl(['/lists/view', 'slug' => $list->slug]) ?>">
							<img alt="<?php echo $list->lastEntry->title; ?>" <?php echo $list->lastEntry->image; ?>>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else: ?>
		<div class="alert alert-info"><?php echo Yii::t('Lists', 'Currently no lists are available.'); ?></div>
	<?php endif; ?>
</div>
