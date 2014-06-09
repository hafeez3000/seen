<script type="text/javascript">
	var App = {
		baseUrl: "<?php echo Yii::$app->request->baseUrl; ?>",
		language: "<?php echo Yii::$app->language; ?>",
		themoviedb: {
			key: "<?php echo Yii::$app->params['themoviedb']['key']; ?>",
			url: "<?php echo Yii::$app->params['themoviedb']['url']; ?>",
			image_url: "<?php echo Yii::$app->params['themoviedb']['image_url']; ?>"
		},
		translation: {
			unknown_error: "<?php echo Yii::t('Error', 'An unknown error occured! Please try again later.'); ?>",
			first_aired: "<?php echo Yii::t('Show', 'First aired'); ?>",
			released: "<?php echo Yii::t('Movie', 'Released'); ?>",
			votes: "<?php echo Yii::t('Show', 'Votes'); ?>",
			tv_search: "<?php echo Yii::t('Show', 'Search for TV Shows') ?>",
			movie_search: "<?php echo Yii::t('Show', 'Search for Movies') ?>",
			highcharts: {
				decimalPoint: "<?php echo Yii::t('Highcharts', '.'); ?>",
				months: [
					"<?php echo Yii::t('Highcharts', 'January'); ?>",
					"<?php echo Yii::t('Highcharts', 'February'); ?>",
					"<?php echo Yii::t('Highcharts', 'March'); ?>",
					"<?php echo Yii::t('Highcharts', 'April'); ?>",
					"<?php echo Yii::t('Highcharts', 'May'); ?>",
					"<?php echo Yii::t('Highcharts', 'June'); ?>",
					"<?php echo Yii::t('Highcharts', 'July'); ?>",
					"<?php echo Yii::t('Highcharts', 'August'); ?>",
					"<?php echo Yii::t('Highcharts', 'September'); ?>",
					"<?php echo Yii::t('Highcharts', 'October'); ?>",
					"<?php echo Yii::t('Highcharts', 'November'); ?>",
					"<?php echo Yii::t('Highcharts', 'December'); ?>"
				],
				shortMonths: [
					"<?php echo Yii::t('Highcharts', 'Jan'); ?>",
					"<?php echo Yii::t('Highcharts', 'Feb'); ?>",
					"<?php echo Yii::t('Highcharts', 'Mar'); ?>",
					"<?php echo Yii::t('Highcharts', 'Apr'); ?>",
					"<?php echo Yii::t('Highcharts', 'May'); ?>",
					"<?php echo Yii::t('Highcharts', 'Jun'); ?>",
					"<?php echo Yii::t('Highcharts', 'Jul'); ?>",
					"<?php echo Yii::t('Highcharts', 'Aug'); ?>",
					"<?php echo Yii::t('Highcharts', 'Sep'); ?>",
					"<?php echo Yii::t('Highcharts', 'Oct'); ?>",
					"<?php echo Yii::t('Highcharts', 'Nov'); ?>",
					"<?php echo Yii::t('Highcharts', 'Dec'); ?>"
				],
				thousandsSep: "<?php echo Yii::t('Highcharts', ','); ?>",
				weekdays: [
					"<?php echo Yii::t('Highcharts', 'Sunday'); ?>",
					"<?php echo Yii::t('Highcharts', 'Monday'); ?>",
					"<?php echo Yii::t('Highcharts', 'Tuesday'); ?>",
					"<?php echo Yii::t('Highcharts', 'Wednesday'); ?>",
					"<?php echo Yii::t('Highcharts', 'Thursday'); ?>",
					"<?php echo Yii::t('Highcharts', 'Friday'); ?>",
					"<?php echo Yii::t('Highcharts', 'Saturday'); ?>"
				],
				rangeFrom: "<?php echo Yii::t('Highcharts', 'From'); ?>",
				rangeTo: "<?php echo Yii::t('Highcharts', 'To'); ?>"
			},
			show_sync: "<?php echo Yii::t('Show', 'Successfully synced {0} shows as {1} seasons'); ?>",
			show_sync_error: "<?php echo Yii::t('Show', 'Errow while syncing the show'); ?>"
		}
	}

	var _paq = _paq || [];
</script>
