if ( typeof CHAMELEON === "undefined" || !CHAMELEON ) { var CHAMELEON = {}; }
CHAMELEON.CORE = CHAMELEON.CORE || {};
CHAMELEON.CORE.pkgArticle = CHAMELEON.CORE.pkgArticle || {};
CHAMELEON.CORE.pkgArticle._lastTargetUrl = null;

CHAMELEON.CORE.pkgArticle.CallAjaxOnList = function(URL, spotName, listIdent, method, jsCallback) {
    CHAMELEON.CORE.pkgArticle._lastTargetUrl = URL;

    var sep = (URL.indexOf('?') === -1) ? '?' : '&';
    var targetURL = URL + sep
        + 'listident=' + encodeURIComponent(listIdent)
        + '&' + encodeURIComponent("module_fnc["+spotName+"]") + '=ExecuteAjaxCall'
        + '&_fnc=' + encodeURIComponent(method);

    $.ajax({
        url: targetURL,
        processData: false,
        dataType: 'json',
        success: jsCallback,
        type: 'POST'
    });

    return false;
};

CHAMELEON.CORE.pkgArticle.LoadArticleCollectionReturn = function(data, responseMessage) {
  var oContainer = $('.pkg-article-list-'+data.sSpotName + '-'+data.sListIdent);
  var oTmp = $(data.sResult);
  oContainer.replaceWith(oTmp);
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

LoadGenericTeaserList = function(sURL, sSpotName, sCallback) {
    if (CHAMELEON.CORE.pkgArticle.aTeaserItemCache[sURL] !== undefined) {
        sCallback(CHAMELEON.CORE.pkgArticle.aTeaserItemCache[sURL],null);
        return false;
    }

    var sSep = '?';
    var patt1=/\?/g;
    if (sURL.match(patt1) == null) sSep = '?';
    else sSep = '&';
    var tmpString = sURL + sSep + encodeURIComponent("module_fnc["+sSpotName+"]") + "=ExecuteAjaxCall&_fnc=GetContentBlock";

    $.ajax({
       url: tmpString,
       processData: false,
       dataType:  'json',
       success: function(data, responseMessage){
           CHAMELEON.CORE.pkgArticle.aTeaserItemCache[sURL] = data;
           sCallback(data,responseMessage);
       },
       type: 'POST'
     });
    return false;
  };

CHAMELEON.CORE.pkgArticle.aGenericTeaserCache = new Array();
CHAMELEON.CORE.pkgArticle.sGenericTeaserUrlRequested = '';
LoadGenericTeaserListShift = function(sURL, sSpotName, sCallback) {
CHAMELEON.CORE.pkgArticle.sGenericTeaserUrlRequested = sURL;
	if (typeof(CHAMELEON.CORE.pkgArticle.aGenericTeaserCache[CHAMELEON.CORE.pkgArticle.sGenericTeaserUrlRequested]) != 'undefined') {
		CHAMELEON.CORE.pkgArticle.LoadGenericTeaserListReturn(CHAMELEON.CORE.pkgArticle.aGenericTeaserCache[CHAMELEON.CORE.pkgArticle.sGenericTeaserUrlRequested],null);
	} else {
	    var sSep = '?';
	    var patt1=/\?/g;
	    if (sURL.match(patt1) == null) sSep = '?';
	    else sSep = '&';
	    var tmpString = sURL + sSep + encodeURIComponent("module_fnc["+sSpotName+"]") + "=ExecuteAjaxCall&_fnc=GetContentBlockShifted";
	    GetAjaxCall(tmpString, sCallback);
	}
    return false;
  };

LoadGenericTeaserListReturn = function(data, responseMessage) {
  CHAMELEON.CORE.pkgArticle.aGenericTeaserCache[CHAMELEON.CORE.pkgArticle.sGenericTeaserUrlRequested] = data;
  var oContainer = $('.pkg-article-teaser-list-'+data.sSpotName);
  var oTmp = $(data.sResult);
  oContainer.replaceWith(oTmp);

};