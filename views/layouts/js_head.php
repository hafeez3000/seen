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
			movie_search: "<?php echo Yii::t('Show', 'Search for Movies') ?>"
		}
	}

	var _paq = _paq || [];
</script>
