if ( typeof CHAMELEONPKGMULTIMODULE=== "undefined" || !CHAMELEONPKGMULTIMODULE ) { var CHAMELEONPKGMULTIMODULE = {}; }

CHAMELEONPKGMULTIMODULE.GetPkgMultiModuleAjaxCall = function GetPkgMultiModuleAjaxCall(url, functionName) {
  url = url +'&'+esPHPSESSION;
  $.ajax({
     url: url,
     processData: false,
     dataType:  'json',
     success: functionName,
     type: 'POST'
   });
};

CHAMELEONPKGMULTIMODULE.RenderModule = function RenderModule(aData){
    var container = document.createElement("div");
    container.innerHTML = aData.html;
    $container = $(container);
    $(container).attr("id",aData.sModuleContentIdentifier) ;
    var $productsListParent = $('#'+aData.sModuleContentIdentifier);
    $productsListParent.parent().append($container);
    $productsListParent.remove();
}

CHAMELEONPKGMULTIMODULE.Init = function() {
};

$(document).ready(function() {
    CHAMELEONPKGMULTIMODULE.Init();
});