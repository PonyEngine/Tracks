var pkBaseURL = (("https:" == document.location.protocol) ? "https://www.savalascolbert.com/analytics/" : "http://www.savalascolbert.com/analytics/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
try {
    var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 5);
    piwikTracker.trackPageView();
    piwikTracker.enableLinkTracking();
} catch( err ) {}
<noscript><p><img src="http://www.savalascolbert.com/analytics/piwik.php?idsite=5" style="border:0" alt="" /></p></noscript>