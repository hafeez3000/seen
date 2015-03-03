<?php if (!defined('YII_DEBUG') || !YII_DEBUG): ?>
	<!-- Piwik -->
	<script type="text/javascript">
		_paq.push(["setCookieDomain", "*.seenapp.com"]);
		_paq.push(['trackPageView']);
		_paq.push(['enableLinkTracking']);

		(function() {
			var u=(("https:" == document.location.protocol) ? "https" : "http") + "://stats.visualappeal.de/";
			_paq.push(['setTrackerUrl', u+'piwik.php']);
			_paq.push(['setSiteId', 20]);
			_paq.push(['setCustomVariable',
				1,
				"Language",
				"<?php echo Yii::$app->language; ?>",
				"visit"
			]);
			var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
			g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
		})();
	</script>
	<noscript><p><img src="http://stats.visualappeal.de/piwik.php?idsite=20" style="border:0;" alt="" /></p></noscript>
	<!-- End Piwik -->

	<!-- Quantcast -->
	<script type="text/javascript">
		var _qevents = _qevents || [];

		(function() {
		var elem = document.createElement('script');
		elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
		elem.async = true;
		elem.type = "text/javascript";
		var scpt = document.getElementsByTagName('script')[0];
		scpt.parentNode.insertBefore(elem, scpt);
		})();

		_qevents.push({
		qacct:"p-nKZ9NZ7qa45fn"
		});
	</script>
	<noscript>
		<div style="display:none;">
			<img src="//pixel.quantserve.com/pixel/p-nKZ9NZ7qa45fn.gif" border="0" height="1" width="1" alt="Quantcast"/>
		</div>
	</noscript>
	<!-- End Quantcast -->
<?php endif; ?>
