if ( typeof CHAMELEON === "undefined" || !CHAMELEON ) { var CHAMELEON = {}; }
CHAMELEON.CORE = CHAMELEON.CORE || {};
CHAMELEON.CORE.pkgArticle = CHAMELEON.CORE.pkgArticle || {};

CHAMELEON.CORE.pkgArticle.CallAjaxOnList = function(sURL, sSpotName, sListIdent, sMethod, sJsCallback) {
  var sSep = '?';
  var patt1=/\?/g;
  if (sURL.match(patt1) == null) sSep = '?';
  else sSep = '&';
  sTargetURL = sURL
  var sTargetURL = sURL + sSep +'listident='+encodeURIComponent(sListIdent) +'&' + encodeURIComponent("module_fnc["+sSpotName+"]") + "=ExecuteAjaxCall&_fnc="+encodeURIComponent(sMethod);

  $.ajax({
     url: sTargetURL,
     processData: false,
     dataType:  'json',
     success: sJsCallback,
     type: 'POST'
   });
  return false;
};

CHAMELEON.CORE.pkgArticle.LoadArticleCollectionReturn = function(data, responseMessage) {
  var oContainer = $('.pkg-article-list-'+data.sSpotName + '-'+data.sListIdent);
  var oTmp = $(data.sResult);
  oContainer.replaceWith(oTmp);
};

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