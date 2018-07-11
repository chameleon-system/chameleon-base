  function ShowNewBrandTeaser(data,responseMessage) {
    var container = document.createElement("div");
    container.innerHTML = data;
    $container = $(container);

    var brandStoreOld = $('.TdbPkgShopBrandStore');
    var brandStoreContainer = brandStoreOld.parent();


    brandStoreOld.remove();

    brandStoreContainer.append(data);
    $('.TdbPkgShopBrandStore').hide().fadeIn("slow");
    //$('.TdbPkgShopBrandStore').fadeIn("slow");
  }