<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

use \app\components\LanguageHelper;
?>

<div class="panel panel-default email-view<?php if ($email->responded !== null): ?> email-view-responded<?php endif; ?>" id="email-view-<?php echo $email->id ?>">
	<h4 class="panel-heading">
		<div class="clearfix">
			<div class="pull-left">
				<?php echo !empty($email->subject) ? Html::encode($email->subject) : '<em>' . Yii::t('Email', 'No Subject') . '</em>'; ?>
				&nbsp;&dash;&nbsp;
				<a href="<?php echo Url::toRoute(['email/reply', 'id' => $email->id]); ?>">
					<?php echo (empty($email->from_name)) ? Html::encode($email->from_email) : Html::encode($email->from_name); ?>
				</a>
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