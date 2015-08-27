<?php
use \yii\helpers\Url;

$this->title[] = Yii::t('Admin', 'Missing Episodes');
?>

<h1><?php echo Yii::t('Admin', 'Missing Episodes'); ?></h1>

<p>
	<a href="<?php echo Url::toRoute(['sync-missing']); ?>" class="btn btn-info"><?php echo Yii::t('Admin', 'Sync missing'); ?></a>
</p>

<ul>
	<?php foreach ($seasons as $season): ?>
		<li><a href="https://www.themoviedb.org/tv/<?php echo $season['themoviedb_id']; ?>/season/<?php echo $season['number']; ?>" target="_blank"><?php echo $season['original_name']; ?> (S<?php echo $season['number']; ?>)</a></li>
	<?php endforeach; ?>
</ul>
