<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
?>

<div id="tv-view">
	<div class="container">
		<h1><?php echo Html::encode($show->name); ?></h1>

		<div class="row">
			<?php echo $this->render('/tv/_seasons', [
				'show' => $show,
				'seasons' => $show->seasons,
			]) ?>

			<div class="col-sm-8 col-md-9 col-lg-10">
				<div class="row">
					<div class="col-md-6">
						<div class="overview">
							<?php echo Html::encode($show->overview); ?>
						</div>

						<?php if (count($show->cast)): ?>
							<div class="cast">
								<h2><?php echo Yii::t('Show/View', 'Cast'); ?></h2>

								<ul id="tv-view-cast">
									<?php foreach ($show->cast as $cast): ?>
										<li>
											<?php echo Html::encode($cast->name); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<?php if (count($show->crew)): ?>
							<div class="crew">
								<h2><?php echo Yii::t('Show/View', 'Crew'); ?></h2>

								<ul id="tv-view-crew">
									<?php foreach ($show->crew as $crew): ?>
										<li>
											<?php echo Html::encode($crew->name); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<div class="rating">

						</div>
					</div>
					<div class="col-md-6">
						<img src="<?php echo $show->posterLarge; ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>