<?php

return yii\helpers\ArrayHelper::merge(
	require(__DIR__ . '/../../config/testing/console.php'),
	require(__DIR__ . '/../_config.php')
);
