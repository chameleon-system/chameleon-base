function ShowLargeVideo(imageId, width, height, videoID, refererPageId) {
  if ($.browser.msie) {
    // ie sendevent doesn`t work at the moment
    if(thisMovie(videoID).sendEvent) {
      thisMovie(videoID).sendEvent('stop');
    }
  } else {
    thisMovie(videoID).sendEvent('stop');
  }
  window.open('/?pagedef=showvideo&id='+imageId+'&width='+width+'&height='+height+'&refererPageId='+refererPageId, '_blank','width='+width+',height='+height+',resizable=yes,location=no,menubar=no,scrollbars=no,status=no,toolbar=no');
}

// This is a javascript handler for the player and is always needed.
function thisMovie(movieName) {
  if(navigator.appName.indexOf("Microsoft") != -1) {
    return window[movieName];
  } else {
    if(document[movieName].length != undefined) return document[movieName][1];
    return document[movieName];
  }
};

// calls this method to init antispam. thickbox etc.
function GlobalAjaxInit()
{
  tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
	imgLoader = new Image();// preload image
	imgLoader.src = tb_pathToImage;
	DoAntiSpam(atSymbol, dotSymbol);
}

function modifyemail(emailitem, atSymbol, dotSymbol){
  var exp = RegExp('\\['+atSymbol+'\\]', 'ig');
  var modified = emailitem.replace(exp, "@");
  modified = modified.replace(/mailto:/gi, "");
  exp = RegExp('\\['+dotSymbol+'\\]', 'ig');
  modified = modified.replace(exp, ".");
  modified = modified.replace(/ /gi, "");
  return modified
}

function DoAntiSpam(atSymbol, dotSymbol) {
  var aantispam=$(".antispam");
  for(i=0;i<aantispam.length;i++) {
    var antispam = aantispam[i];
    var newhref = $(antispam).attr('href');
    var newhref = modifyemail(newhref,atSymbol, dotSymbol);
    $(antispam).attr('href','mailto:'+newhref);
    var text = $(antispam).html();
      if(text.search('['+atSymbol+']')!=-1) {
      text=modifyemail(text,atSymbol, dotSymbol);
      $(antispam).html(text);
    }
  }
}