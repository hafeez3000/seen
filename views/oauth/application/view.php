<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;
use \yii\helpers\Html;

$this->title[] = Yii::t('Developer', '{name}', ['name' => $model->name]);
$this->title[] = Yii::t('Developer', 'Developer');
?>

<?php echo $this->render('/developer/_header', [
	'active' => 'oauth-application/view',
]); ?>


<h1><?php echo Html::encode($model->name); ?></h1>

<div id="oauth-consumer-view">
	<table class="table table-striped">
		<tbody>
			<tr>
				<td><?php echo $model->getAttributeLabel('description'); ?></td>
				<td><?php echo Html::encode($model->description); ?></td>
			</tr>
			<tr>
				<td><?php echo $model->getAttributeLabel('website'); ?></td>
				<td><a href="<?php echo $model->website; ?>"><?php echo Html::encode($model->website); ?></a></td>
			</tr>
			<tr>
				<td><?php echo $model->getAttributeLabel('callback'); ?></td>
				<td><a href="<?php echo $model->callback; ?>"><?php echo Html::encode($model->callback); ?></a></td>
			</tr>
			<tr>
				<td><?php echo $model->getAttributeLabel('key'); ?></td>
				<td><input type="text" value="<?php echo $model->key; ?>" class="form-control autoselect" readonly="readonly"></td>
			</tr>
			<tr>
				<td><?php echo $model->getAttributeLabel('secret'); ?></td>
				<td>
					<?php if ($showSecret): ?>
						<input type="text" value="<?php echo $model->secret; ?>" class="form-control autoselect" readonly="readonly">
					<?php else: ?>
						xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx <a href="<?php echo Url::toRoute(['oauth-application/view', 'id' => $model->id, 'showSecret' => true]); ?>"><?php echo Yii::t('Oauth/Application', 'Show secret'); ?></a>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>

	<p class="clearfix">
		<div class="pull-left">
			<a href="<?php echo Url::toRoute(['update', 'id' => $model->id]); ?>" class="btn btn-default"><?php echo Yii::t('Oauth/Application', 'Update details'); ?></a>
			<a href="<?php echo Url::toRoute(['regenerate', 'id' => $model->id]); ?>" class="btn btn-warning"><?php echo Yii::t('Oauth/Application', 'Regenerate API keys'); ?></a>
		</div>

		<div class="pull-right">
			<a href="<?php echo Url::toRoute(['delete', 'id' => $model->id]); ?>" class="btn btn-danger"><?php echo Yii::t('Oauth/Application', 'Delete application'); ?></a>
		</div>
	</p>
</div>

<?php echo $this->render('/developer/_footer');