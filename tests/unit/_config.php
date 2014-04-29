<?php

return yii\helpers\ArrayHelper::merge(
	require(__DIR__ . '/../../config/testing/web.php'),
	require(__DIR__ . '/../_config.php')
);
