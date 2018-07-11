  function ShopOrderSendConfirmOrderMail(targetUrl, sTargetEmail) {
    newEMail = prompt(CHAMELEON.CORE.i18n.Translate("chameleon_system_core.js.prompt_order_confirm_target_mail"),sTargetEmail);
    targetUrl = targetUrl + '&sTargetMail='+encodeURIComponent(newEMail);
    GetAjaxCall(targetUrl, ShopOrderSendConfirmOrderMailPostCallHook);
  }

  function ShopOrderSendConfirmOrderMailPostCallHook(data,statusText) {
      var aUrlParts = data.replace("//","").split('/');
      var aCurrentUrlParts = window.location.href.replace("//","").split('/');
      if(aUrlParts[0] != aCurrentUrlParts[0]) {
          CloseModalIFrameDialog();

          toasterMessage(CHAMELEON.CORE.i18n.Translate("chameleon_system_core.js.wrong_domain"));
      }
      $.ajax({
          url:data,
          processData:false,
          dataType:'json',
          success:ShopOrderSendConfirmOrderMailReturnFromFrontendCall,
          type:'POST'
      });
  }

  function ShopOrderSendConfirmOrderMailReturnFromFrontendCall(data) {
      CloseModalIFrameDialog();
      toasterMessage(data.sMessage,data.sMessageType);
  }