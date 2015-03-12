<?php

use \yii\helpers\Html;
use \yii\helpers\Markdown;

?>

<div class="col-md-10 col-lg-8 list-entry">
	<h3>#<?php echo $model->position; ?> <?php echo Html::encode($model->title); ?></a></h3>

	<figure>
		<a href="<?php echo $model->permalink; ?>">
			<img <?php echo $model->image; ?> alt="<?php echo Html::encode($model->title); ?>">
		</a>

		<figcaption class="clearfix">
			<p class="text-muted">
				<?php echo Markdown::process($model->description); ?>
			</p>
		</figcaption>
	</figure>
</div>
