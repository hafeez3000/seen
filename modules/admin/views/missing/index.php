<?php
use \yii\helpers\Url;

$this->title[] = Yii::t('Admin', 'Missing Episodes');
?>

<h1><?php echo Yii::t('Admin', 'Missing Episodes'); ?></h1>

<p>
	<a href="<?php echo Url::toRoute(['sync-missing']); ?>" class="btn btn-info"><?php echo Yii::t('Admin', 'Sync missing'); ?></a>
</p>

<script src="https://cdnjs.cloudflare.com/ajax/libs/async/1.4.2/async.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$(".sync-season").on('click', function(e) {
			e.preventDefault();

			var $element = $(this);
			var id = $element.data('id');
			var count = $element.data('count');
			var season = $element.data('season');
			var offsets = [];

			for (var offset = 0; offset < count; offset++) {
				offsets.push(offset);
			}

			console.info("Sync", offset, "seasons of season", season, "from show", id);

			$element.html("<div class='spinner-loader' style='font-size: 3px; font-color: #333;'>Loading...</div>");

			async.eachLimit(offsets, 4, function(offset, cb) {
				console.debug("Starting to sync offset", offset);
				var url = "<?php echo Yii::$app->urlManager->createAbsoluteUrl(['/admin/missing/sync-multiple']); ?>";
				var data = {
					"id": id,
					"season": season,
					"offset": offset
				};
				$.getJSON(url, data, function(result) {
					if (result.success && result.success == true) {
						return cb();
					} else {
						console.error("Error while syncing offset", result);
						return cb(result);
					}
				});
			}, function(err) {
				if (!err) {
					$element.parent().append("<i class='glyphicon glyphicon-ok'></i>");
					$element.hide();
				} else {
					console.error(err);
					$("#flash-messages").append("<div class='alert alert-danger'>Error while syncing a season: " + err + "</div>");
				}
			})

			return false;
		});
	});
</script>

<ul>
	<?php foreach ($seasons as $season): ?>
		<li><a href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['/']); ?>tv/<?php echo $season['themoviedb_id']; ?>" title="Show on SEEN"><?php echo $season['original_name']; ?> (S<?php echo $season['number']; ?>)</a> <a href="https://www.themoviedb.org/tv/<?php echo $season['themoviedb_id']; ?>/season/<?php echo $season['number']; ?>" title="Show on TheMovieDB"><i class="glyphicon glyphicon-share"></i></a> <a href="#" class="sync-season" data-id="<?php echo $season['themoviedb_id']; ?>" data-season="<?php echo $season['number']; ?>" title="Sync missing episodes"><i class="glyphicon glyphicon-refresh"></i></a></li>
	<?php endforeach; ?>
</ul>
