<?php

use \yii\helpers\Url;

use \app\modules\admin\assets\AdminAsset;

AdminAsset::register($this);

?>

<h2><?php echo Yii::t('Statistic', 'User actions in the last 30 days'); ?></h2>

<div id="chart-user-action-timeline" data-url="<?php echo Url::toRoute(['load-user-action-timeline']); ?>" style="width: 100%; height: 300px;"></div>

<h2><?php echo Yii::t('Statistic', 'API calls in the last 30 days'); ?></h2>

<div id="chart-api-call-timeline" data-url="<?php echo Url::toRoute(['load-api-call-timeline']); ?>" style="width: 100%; height: 300px;"></div>

<h2><?php echo Yii::t('Statistic', 'Most popular TV Shows'); ?></h2>

<div id="chart-popular-tv" data-url="<?php echo Url::toRoute(['load-popular-tv']); ?>" style="width: 100%; height: 500px;"></div>

<h2><?php echo Yii::t('Statistic', 'Most popular Movies'); ?></h2>

<div id="chart-popular-movie" data-url="<?php echo Url::toRoute(['load-popular-movie']); ?>" style="width: 100%; height: 500px;"></div>
