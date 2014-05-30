<?php

use \yii\helpers\Url;

use \app\modules\admin\assets\AdminAsset;

AdminAsset::register($this);

?>

<h2><?php echo Yii::t('Statistic', 'User actions in the last 30 days'); ?></h2>

<div id="chart-user-action-timeline" data-url="<?php echo Url::toRoute(['load-user-action-timeline']); ?>" style="width: 100%; height: 300px;"></div>
