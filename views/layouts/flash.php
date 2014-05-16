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
				<?php } elseif ($key == 'event') {
					$data = unserialize($message);
				?>
					<script type="text/javascript">
						_paq.push([
							'trackEvent',
							"<?php echo isset($data['category']) ? $data['category'] : ''; ?>",
							"<?php echo isset($data['action']) ? $data['action'] : ''; ?>",
							"<?php echo isset($data['name']) ? $data['name'] : ''; ?>",
							"<?php echo isset($data['value']) ? $data['value'] : ''; ?>"
						]);
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
