function MTPkgImageHotspot_LoadNextItem(sContainerId, iDelayInSeconds) {
  $('#'+sContainerId).find('.nextItemLink').click();
  setTimeout(function(){MTPkgImageHotspot_LoadNextItem(sContainerId, iDelayInSeconds)},iDelayInSeconds*1000);// 10000
}
