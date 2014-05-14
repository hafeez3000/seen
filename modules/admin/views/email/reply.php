<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Url;
use \yii\helpers\Html;
use \yii\widgets\ActiveForm;

use \app\components\LanguageHelper;

$this->title[] = $email->subject;
$this->title[] = Yii::t('Email', 'Emails');
?>

<div id="email-reply">
	<div class="row">
		<div class="col-md-6">
			<div id="email-reply-form-affix">
				<div class="panel panel-primary">
					<h4 class="panel-heading"><?php echo Yii::t('Email/Reply', 'Reply to: {subject}', [
						'subject' => !empty($email->subject) ?
							Html::encode($email->subject) :
							'<em class="text-muted">' . Yii::t('Email/Reply', 'No subject') . '</em>'
						]); ?></h4>

					<div class="panel-body">
						<?php $form = ActiveForm::begin([
							'id' => 'email-reply-form',
						]); ?>

						<?php echo $form->field($model, 'receiver')->textInput(['disabled' => 'disabled']); ?>

						<?php echo $form->field($model, 'subject')->textInput(); ?>

						<?php echo $form->field($model, 'text')->textarea([
							'rows' => 10,
						]); ?>

						<div class="form-group">
							<?php echo Html::submitButton(Yii::t('Email/Reply', 'Reply'), ['class' => 'btn btn-primary', 'name' => 'reply-button']); ?>
						</div>

						<?php ActiveForm::end(); ?>
					</div>
				</div>
			</div>
		</div>

		<div id="email-reply-history" class="col-md-6">
			<div id="email-reply-history-main">
				<div class="panel panel-default email-view<?php if ($email->responded !== null): ?> email-view-responded<?php endif; ?>" id="email-view-<?php echo $email->id ?>">
					<h4 class="panel-heading">
						<div class="clearfix">
							<div class="pull-left">
								<?php echo !empty($email->subject) ? Html::encode($email->subject) : '<em>' . Yii::t('Email', 'No Subject') . '</em>'; ?>
							</div>

							<div class="pull-right text-muted">
								<?php echo LanguageHelper::dateTime(strtotime($email->ts)); ?>
							</div>
						</div>
					</h4>

					<div class="panel-body">
						<?php echo nl2br(Html::encode($email->text)); ?>
					</div>
				</div>
			</div>

			<div id="email-reply-history-recent">
				<?php foreach ($emails as $email): ?>
					<div class="panel panel-default email-view<?php if ($email->responded !== null): ?> email-view-responded<?php endif; ?>" id="email-view-<?php echo $email->id ?>">
						<h4 class="panel-heading">
							<div class="clearfix">
								<div class="pull-left">
									<?php echo !empty($email->subject) ? Html::encode($email->subject) : '<em>' . Yii::t('Email', 'No Subject') . '</em>'; ?>
								</div>

								<div class="pull-right text-muted">
									<?php echo LanguageHelper::dateTime(strtotime($email->ts)); ?>
								</div>
							</div>
						</h4>

						<div class="panel-body">
							<?php echo nl2br(Html::encode($email->text)); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>