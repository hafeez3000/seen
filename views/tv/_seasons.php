<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
?>

<div class="col-sm-4 col-md-3 col-lg-2">
	<div class="list-group" id="tv-view-seasons">
		<?php foreach ($seasons as $season): ?>
			<?php if (count($season->episodes) > 0): ?>
				<a class="list-group-item" href="<?php echo Url::toRoute(['season/view', 'slug' => $show->slug, 'number' => $season->number]) ?>">
					<span class="badge"><?php echo $season->latestUserEpisodesCount; ?>/<?php echo count($season->episodes); ?></span>
					<?php echo Html::encode($season->fullName); ?>
				</a>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>