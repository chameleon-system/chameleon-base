if ( typeof CHAMELEON === "undefined" || !CHAMELEON ) { var CHAMELEON = {}; }
CHAMELEON.CORE = CHAMELEON.CORE || {};
CHAMELEON.CORE.pkgArticle = CHAMELEON.CORE.pkgArticle || {};
CHAMELEON.CORE.pkgArticle._lastTargetUrl = null;

CHAMELEON.CORE.pkgArticle.CallAjaxOnList = function(url, spotName, listIdent, method, jsCallback) {
    CHAMELEON.CORE.pkgArticle._lastTargetUrl = url;

    var sep = (url.indexOf('?') === -1) ? '?' : '&';
    var targeturl = url + sep
        + 'listident=' + encodeURIComponent(listIdent)
        + '&' + encodeURIComponent("module_fnc["+spotName+"]") + '=ExecuteAjaxCall'
        + '&_fnc=' + encodeURIComponent(method);

    fetch(targeturl, {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        }
    }).then(function(response) {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    }).then(function(data){
        if (typeof jsCallback === 'function') {
            jsCallback(data);
        }
    }).catch(function(err){
        if (window && window.console && console.error) {
            console.error('Ajax error (CallAjaxOnList):', err);
        }
    });

    return false;
};

CHAMELEON.CORE.pkgArticle.LoadArticleCollectionReturn = function(data, responseMessage) {
  var container = document.querySelector('.pkg-article-list-'+data.sSpotName + '-'+data.sListIdent);
  if (container) {
    container.outerHTML = data.sResult;
  }
    // Refresh URL/History (Deep Linking + Back/Forward)
    if (CHAMELEON.CORE.pkgArticle._lastTargetUrl) {
        history.pushState(
            { spot: data.sSpotName, listIdent: data.sListIdent, url: CHAMELEON.CORE.pkgArticle._lastTargetUrl },
            '',
            CHAMELEON.CORE.pkgArticle._lastTargetUrl
        );
    }
};
window.addEventListener('popstate', function(e) {
    if (!e.state || !e.state.url) {
        // Fallback: if there's no state, classic navigation
        location.reload();
        return;
    }

    CHAMELEON.CORE.pkgArticle.CallAjaxOnList(
        e.state.url,
        e.state.spot,
        e.state.listIdent,
        'ChangePage',
        CHAMELEON.CORE.pkgArticle.LoadArticleCollectionReturn
    );
});

CHAMELEON.CORE.pkgArticle.aTeaserItemCache = {};

LoadGenericTeaserList = function(url, spotName, callback) {
    if (CHAMELEON.CORE.pkgArticle.aTeaserItemCache[url] !== undefined) {
        callback(CHAMELEON.CORE.pkgArticle.aTeaserItemCache[url],null);
        return false;
    }

    var sep = (url.indexOf('?') === -1) ? '?' : '&';
    var targetUrl = url + sep
        + encodeURIComponent("module_fnc["+spotName+"]")
        + "=ExecuteAjaxCall"
        + "&_fnc=GetContentBlock";

    fetch(targetURL, {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        }
    }).then(function(response){
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    }).then(function(data){
        CHAMELEON.CORE.pkgArticle.aTeaserItemCache[url] = data;
        if (typeof callback === 'function') {
            callback(data, null);
        }
    }).catch(function(err){
        if (window && window.console && console.error) {
            console.error('Ajax error (LoadGenericTeaserList):', err);
        }
    });
    return false;
};

CHAMELEON.CORE.pkgArticle.aGenericTeaserCache = new Array();
CHAMELEON.CORE.pkgArticle.sGenericTeaserUrlRequested = '';

LoadGenericTeaserListShift = function(url, spotName, callback) {
    CHAMELEON.CORE.pkgArticle.sGenericTeaserUrlRequested = url;
	if (typeof(CHAMELEON.CORE.pkgArticle.aGenericTeaserCache[CHAMELEON.CORE.pkgArticle.sGenericTeaserUrlRequested]) != 'undefined') {
		CHAMELEON.CORE.pkgArticle.LoadGenericTeaserListReturn(CHAMELEON.CORE.pkgArticle.aGenericTeaserCache[CHAMELEON.CORE.pkgArticle.sGenericTeaserUrlRequested],null);
	} else {
        var sep = (URL.indexOf('?') === -1) ? '?' : '&';
	    var targetURL = url + sep
            + encodeURIComponent("module_fnc["+spotName+"]")
            + "=ExecuteAjaxCall"
            + "&_fnc=GetContentBlockShifted";
	    GetAjaxCall(targetURL, callback);
	}
    return false;
};

LoadGenericTeaserListReturn = function(data, responseMessage) {
  CHAMELEON.CORE.pkgArticle.aGenericTeaserCache[CHAMELEON.CORE.pkgArticle.sGenericTeaserUrlRequested] = data;
  var container = document.querySelector('.pkg-article-teaser-list-'+data.sSpotName);
  if (container) {
    container.outerHTML = data.sResult;
  }
};