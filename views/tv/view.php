<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
?>

<div id="tv-view">
	<div class="container">
		<div class="clearfix">
			<div class="pull-left">
				<h1><?php echo Html::encode($show->name); ?></h1>
			</div>

			<div class="pull-right" id="missing-information">
				<a href="https://www.themoviedb.org/tv/<?php echo $show->themoviedb_id; ?>?<?php echo http_build_query(['language' => $show->language->iso]) ?>" target="_blank"  title="<?php echo Yii::t('Season/View', 'Update information on The Movie Database') ?>">
					<?php echo Yii::t('Show/View', 'Missing information?') ?>
				</a>
			</div>
		</div>

		<div class="row">
			<?php echo $this->render('/tv/_seasons', [
				'show' => $show,
				'seasons' => $show->seasons,
			]) ?>

			<div class="col-sm-8 col-md-9 col-lg-10">
				<div class="row">
					<div class="col-md-6" id="tv-view-content">
						<?php if (!empty($show->overview)): ?>
							<div id="tv-view-overview">
								<?php echo Html::encode($show->overview); ?>
							</div>
						<?php endif; ?>

						<?php if (count($show->cast)): ?>
							<div id="tv-view-cast-wrapper" class="persons">
								<h2><?php echo Yii::t('Show/View', 'Cast'); ?></h2>

								<ul id="tv-view-cast" class="list-unstyled list-inline">
									<?php foreach ($show->cast as $cast): ?>
										<li>
											<img src="<?php echo $cast->profileUrl; ?>" alt="<?php echo Html::encode($cast->name); ?>" title="<?php echo Html::encode($cast->name); ?>">
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<?php if (count($show->crew)): ?>
							<div id="tv-view-crew-wrapper" class="persons">
								<h2><?php echo Yii::t('Show/View', 'Crew'); ?></h2>

								<div id="tv-view-crew" class="list-unstyled">
									<?php foreach ($show->crew as $crew): ?>
										<?php echo Html::encode($crew->name); ?>,&nbsp;
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<div class="rating">

						</div>
					</div>
					<div class="col-md-6" id="tv-view-poster">
						<img src="<?php echo $show->posterLargeUrl; ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>