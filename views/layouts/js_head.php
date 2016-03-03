<script type="text/javascript">
	var App = {
		baseUrl: "<?php echo Yii::$app->params['baseUrl']; ?>",
		language: "<?php echo Yii::$app->language; ?>",
		themoviedb: {
			key: "<?php echo Yii::$app->params['themoviedb']['key']; ?>",
			url: "<?php echo Yii::$app->params['themoviedb']['url']; ?>",
			image_url: "<?php echo Yii::$app->params['themoviedb']['image_url']; ?>"
		},
		urls: {
			datatv: "<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tv/load']); ?>",
			datamovie: "<?php echo Yii::$app->urlManager->createAbsoluteUrl(['movie/load']); ?>",
			dataperson: "<?php echo Yii::$app->urlManager->createAbsoluteUrl(['person/load']); ?>"
		},
		translation: {
			unknown_error: "<?php echo Yii::t('Error', 'An unknown error occured! Please try again later.'); ?>",
			first_aired: "<?php echo Yii::t('Show', 'First aired'); ?>",
			released: "<?php echo Yii::t('Movie', 'Released'); ?>",
			votes: "<?php echo Yii::t('Show', 'Votes'); ?>",
			original_title: "<?php echo Yii::t('Show', 'Original Title'); ?>",
			search: "<?php echo Yii::t('Show', 'Search for TV Shows/Movies/Actors') ?>",
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

	<?php if (Yii::$app->user->identity === null): ?>
		App.user = {
			"guest": true
		};
	<?php else: ?>
		App.user = {
			"guest": false,
			"id": <?php echo Yii::$app->user->identity->id; ?>,
			"email": "<?php echo Yii::$app->user->identity->email; ?>",
			"language": "<?php echo isset(Yii::$app->user->identity->language->name) ? Yii::$app->user->identity->language->name : 'English'; ?>",
			"timezone": "<?php echo Yii::$app->user->identity->timezone; ?>",
			"themoviedb_account": <?php echo !empty(Yii::$app->user->identity->themoviedb_account_id) ? 'true' : 'false'; ?>,
			"public_profile": <?php echo Yii::$app->user->identity->profile_public ? 'true' : 'false'; ?>,
			"registered_at": "<?php echo Yii::$app->user->identity->created_at; ?>"
		}
	<?php endif; ?>

	// Piwik
	var _paq = _paq || [];
</script>

<?php if (isset(Yii::$app->params['mixpanel'])): ?>
	<!-- start Mixpanel --><script type="text/javascript">(function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable time_event track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.union people.track_charge people.clear_charges people.delete_user".split(" ");for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src="undefined"!==typeof MIXPANEL_CUSTOM_LIB_URL?MIXPANEL_CUSTOM_LIB_URL:"file:"===e.location.protocol&&"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js".match(/^\/\//)?"https://cdn.mxpnl.com/libs/mixpanel-2-latest.min.js":"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f)}})(document,window.mixpanel||[]); mixpanel.init("<?php echo Yii::$app->params['mixpanel']; ?>");</script><!-- end Mixpanel -->
<?php endif; ?>
