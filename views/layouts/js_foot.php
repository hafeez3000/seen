<?php if (!defined('YII_DEBUG') || YII_DEBUG == false): ?>
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

<script>
	(function(g,i,a,n,t,s){g['SeeYourVisitors']=n;g[n]=g[n]||function(){
	(g[n].q=g[n].q||[]).push(arguments)},g[n].l=1*new Date();t=i.createElement(a),
	s=i.getElementsByTagName(a)[0];t.async=1;t.src='//seeyourvisitors2.appspot.com/gg.js';
	s.parentNode.insertBefore(t,s)})(window,document,'script','gg');
	gg('create', '88226e42-9895-431d-964b-7115f58254c6');
	gg('track');
</script>
<?php endif; ?>
