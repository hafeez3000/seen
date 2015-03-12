<?php
/**
 * @var $this yii\web\View
 * @var $model app\models\Lists
 */

use yii\helpers\Html;
use yii\widgets\DetailView;
use \app\components\LanguageHelper;

$this->title = $model->title;
?>
<div id="lists-view">

	<h1>
		<?php echo Html::encode($this->title) ?>
		<?php if (Yii::$app->user->id === $model->user_id || Yii::$app->user->can('admin')): ?>
			<small>
				<a href="<?php echo Yii::$app->urlManager->createUrl(['/lists-entry/create', 'slug' => $model->slug]); ?>" title="<?php echo Yii::t('Lists', 'Add movie/person/tv show'); ?>">
					<span class="glyphicon glyphicon-plus"></span>
				</a>

				<a href="<?php echo Yii::$app->urlManager->createUrl(['/lists/update', 'slug' => $model->slug]); ?>" title="<?php echo Yii::t('Lists', 'Edit list settings'); ?>">
					<span class="glyphicon glyphicon-pencil"></span>
				</a>
			</small>
		<?php endif; ?>
	</h1>

	<?php echo \yii\helpers\Markdown::process($model->description); ?>

	<p class="text-muted">
		<?php echo Yii::t('List/View', 'Created at {date} by {name}. You can scroll through the list with <kbd>j</kbd> and <kbd>k</kbd>.', ['date' => LanguageHelper::date(strtotime($model->created_at)), 'name' => $model->user->name ?: 'Anonymous']); ?>
	</p>

	<?php if (count($model->entries) > 0): ?>
		<div class="row list-wrapper">
			<?php foreach ($model->entries as $entry): ?>
				<?php echo $this->render('/lists-entry/_view', [
					'model' => $entry,
				]); ?>
			<?php endforeach; ?>
		</div>
	<?php else: ?>
		<div class="alert alert-info"><?php echo Yii::t('Lists', 'This list is empty'); ?></div>
	<?php endif; ?>


</div>
