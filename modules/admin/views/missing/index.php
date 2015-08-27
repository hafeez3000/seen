<?php

use \kartik\grid\GridView;

$this->title[] = Yii::t('Admin', 'Missing Episodes');
?>

<h1><?php echo Yii::t('Admin', 'Missing Episodes'); ?></h1>

<p>
	Syncing season <strong class="current">0</strong> of <strong class="total">0</strong>.
</p>

<h3><?php echo Yii::t('Admin', 'No episodes added'); ?></h3>
<ul class="no-added-episodes">
</ul>

<p class="hidden show-missing-sync-complete">
</p>

<script src="https://cdnjs.cloudflare.com/ajax/libs/async/1.4.2/async.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		var shows = <?php echo json_encode($shows); ?>;
		var i = 0;
		var count = shows.length;
		var url = "<?php echo Yii::$app->urlManager->createAbsoluteUrl(['/admin/missing/sync']); ?>";

		$(".total").html(count);

		async.eachLimit(shows, 4, function(data, cb) {
			i++;
			$(".current").html(i);
			data.i = i;
			$.getJSON(url, data, function(result) {
				console.debug('Synced season', data.id, 'and added', result.added, 'episodes');
				if (result.added == 0) {
					$(".no-added-episodes").append("<li>" + result.i + ": <a href='" + result.url + "'>"  + result.name + "</a> <a href='" + result.edit_url + "' target='_blank'><span class='glyphicon glyphicon-pencil'></span></a></li>");
				}

				return cb();
			});
		}, function(err) {
			if (err) {
				$(".show-missing-sync-complete").html(err);
				$(".show-missing-sync-complete").removeClass("hidden");
				return;
			}

			$(".show-missing-sync-complete").html("All seasons synchronized.");
			$(".show-missing-sync-complete").removeClass("hidden");
		})
	});
</script>
