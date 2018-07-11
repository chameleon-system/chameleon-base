  function TDataCustomListConfigGetAjaxCall(url) {
    $.ajax({
       url: url,
       processData: false,
       dataType:  'json',
       success: TDataCustomListConfigShowItem,
       type: 'POST'
     });
     
  }

  function TDataCustomListConfigShowItem(data,responseMessage) {
    var container = document.createElement("div");
    container.innerHTML = data.sHTML;
    $container = $(container);

    var sContainer = '#TDataCustomListConfigListNr'+data.sDataCustomListConfigId

    var $oListParent = $(sContainer).parent();
    $oListParent.parent().append($container).hide().fadeIn("slow");
    $oListParent.fadeOut("slow").remove();
    GlobalAjaxInit();
  }