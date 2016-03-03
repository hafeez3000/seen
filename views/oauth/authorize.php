<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\widgets\ActiveForm;

$this->title[] = Yii::t('Oauth', 'Authorize {name}', ['name' => $application->name])
?>

<h1><?php echo Yii::t('Oauth', 'Authorize {name}', ['name' => Html::encode($application->name)]); ?></h1>

<div id="oauth-authorize">
	<p id="oauth-authorize-intro">
		<?php echo Yii::t('Oauth', '<strong>{name}</strong> wants to access your data.', [
			'name' => Html::encode($application->name),
		]); ?>
	</p>

	<div class="row">
		<div class="col-md-6" id="oauth-authorize-action">
			<h3><?php echo Yii::t('Oauth', 'Permissions'); ?></h3>

			<ul id="oauth-authorize-permissions" class="list-unstyled">
				<?php foreach ($scopes as $scope): ?>
					<li>
						<h4><?php echo Yii::t('Scope', $scope['name']); ?></h4>
						<p><?php echo Yii::t('Scope', $scope['description']); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>

			<p>
				<?php $form = ActiveForm::begin([
					'id' => 'oauth-authorize',
				]); ?>

				<button type="submit" name="authorize" class="btn btn-success"><?php echo Yii::t('Oauth', 'Authorize application'); ?></button>

				<button type="submit" name="deny" class="btn btn-default"><?php echo Yii::t('Oauth', 'Deny'); ?></button>

				<?php ActiveForm::end(); ?>
			</p>
		</div>

		<div class="col-md-6" id="oauth-authorize-info">
			<h3><?php echo Html::encode($application->name); ?></h3>

			<p>
				<?php echo Html::encode($application->description); ?>
			</p>

			<p>
				<a href="<?php echo Html::encode($application->website); ?>"><?php echo Yii::t('Oauth', 'Visit application website'); ?></a>
			</p>
		</div>
	</div>
</div>
