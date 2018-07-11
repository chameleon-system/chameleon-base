function TCMSShopTableEditor_ShopVoucherSeries_CreateVouchers(sBaseURL, sNumberOfVouchersToCreatePromptText, sVoucherCodeToCreatePromptText) {
    var sNumberOfVouchers = prompt(sNumberOfVouchersToCreatePromptText, 10);
    if(null === sNumberOfVouchers){
        return;
    }
    var sVoucherCode = prompt(sVoucherCodeToCreatePromptText, '');
    if(null === sVoucherCode){
        return;
    }
    iNumberOfVouchers = parseInt(sNumberOfVouchers);
    if (iNumberOfVouchers > 0) {
        GetAjaxCall(sBaseURL + '&iNumberOfVouchers=' + iNumberOfVouchers + '&sCode=' + sVoucherCode, TCMSShopTableEditor_ShopVoucherSeries_CreateVouchersCreated);
    }
}

function TCMSShopTableEditor_ShopVoucherSeries_CreateVouchersCreated(data, type) {
  CloseModalIFrameDialog();
  sType = 'MESSAGE';
  if (data.bError) sType = 'WARNING';
  toasterMessage(data.sMessage,sType);
  //alert(data);
}