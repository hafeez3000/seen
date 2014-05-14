<?php

use \yii\bootstrap\Alert;

?>

<div id="flash-messages">
	<?php
		$flashMessages = Yii::$app->session->getAllFlashes();

		if (is_array($flashMessages)) {
			foreach ($flashMessages as $key => $message) {
				if ($key == 'error')
					$key = 'danger';

				if ($key == 'goal') { ?>
					<script type="text/javascript">
						_paq.push(['trackGoal', <?php echo intval($message); ?>]);
					</script>
				<?php } else {
					echo Alert::widget([
						'options' => [
							'class' => 'alert-' . $key
						],
						'body' => $message,
					]);
				}
			}
		}
	?>
</div>
