<?php
/**
 * @var yii\web\View $this
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
?>

<div id="season-view">
	<div class="container">
		<div class="clearfix">
			<div class="pull-left">
				<h1><a href="<?php echo Url::toRoute(['tv/view', 'slug' => $show->slug]); ?>"><?php echo Html::encode($show->name); ?></a></h1>
			</div>

			<div class="pull-right" id="missing-information">
				<a href="https://www.themoviedb.org/tv/<?php echo $show->themoviedb_id; ?>/season/<?php echo $season->number; ?>?<?php echo http_build_query(['language' => $show->language->iso]) ?>" target="_blank" title="<?php echo Yii::t('Season/View', 'Update season information on The Movie Database') ?>">
					<?php echo Yii::t('Season/View', 'Missing information?') ?>
				</a>
			</div>
		</div>

		<div class="row">
			<?php echo $this->render('/tv/_seasons', [
				'show' => $show,
				'seasons' => $show->seasons,
			]) ?>

			<div class="col-sm-8 col-md-9 col-lg-10">

				<form action="<?php echo Url::toRoute('season/update'); ?>" method="post" data-check-url="<?php echo Yii::$app->urlManager->createAbsoluteUrl('episode/seen'); ?>" data-uncheck-url="<?php echo Yii::$app->urlManager->createAbsoluteUrl('episode/unseen'); ?>">

					<ul id="season-view-episodes" class="list-unstyled">
						<?php foreach ($season->episodes as $episode): ?>
							<li class="<?php if (isset($episodesSeen[$episode->id])): ?>has-seen<?php endif; ?>">
								<label>
									<input type="checkbox" name="<?php echo $episode->id; ?>" <?php if (isset($episodesSeen[$episode->id])): ?>checked="checked"<?php endif; ?>>
									<?php echo Html::encode($episode->fullName); ?>
								</label>
							</li>
						<?php endforeach; ?>
					</ul>

					<p>
						<button type="submit" class="btn btn-primary btn-lg"><?php echo Yii::t('Season/View', 'Save Episodes'); ?></button>
					</p>

				</form>

			</div>
		</div>
	</div>
</div>