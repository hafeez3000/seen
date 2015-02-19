<?php

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\widgets\DetailView;
use \yii\jui\AutoComplete;

$this->title[] = $model->name;
$this->title[] = Yii::t('Authorization', 'Authorization');
?>

<h1><?php echo Yii::t('Authorization', 'Authorization {name}', [
	'name' => $model->name,
]); ?></h1>

<p>
	<a class="btn btn-default" href="<?php echo Url::toRoute(['index']); ?>"><?php echo Yii::t('Language', 'All auth items'); ?></a>
</p>

<?php
echo DetailView::widget([
	'model' => $model,
	'attributes' => [
		'name',
		'type',
		'description',
		'created_at:dateTime',
		'updated_at:dateTime',
	],
]);
?>

<div class="row">
	<?php echo Html::beginForm(['add']); ?>
		<div class="col-xs-10 col-md-5">
			<?php echo AutoComplete::widget([
			    'name' => 'AuthItem[user]',
				'options' => [
					'class' => 'form-control',
				],
			    'clientOptions' => [
					'minLength' => 3,
			        'source' => Url::toRoute(['load']),
			    ],
			]); ?>

			<?php echo Html::activeHiddenInput($model, 'name'); ?>
		</div>

		<div class="col-xs-2 col-md-1">
			<?php echo Html::submitButton(Yii::t('Authorization', 'Add user'), ['class' => 'btn btn-primary']); ?>
		</div>
	<?php echo Html::endForm(); ?>
</div>

<?php if (count($model->users) > 0): ?>
	<h2><?php echo Yii::t('Authorization', 'Users'); ?></h2>

	<ul class="list-unstyled">
		<?php foreach ($model->users as $user): ?>
			<li>
				<?php echo Html::a($user->email, ['user/view', 'id' => $user->id]); ?>
				<a href="<?php echo Url::toRoute(['remove', 'auth' => $model->name, 'user' => $user->id]); ?>"><span class="glyphicon glyphicon-trash"></span></a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif;
