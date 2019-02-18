/**
 * @deprecated since 6.3.0 - not used anymore.
 *
 * methods to manage the THTMLFileBrowser class
 */
  function THTMLFileBrowserSelectAll(formObj, bSelect) {
    for(i=0;i<formObj.elements['aSelectedFiles[]'].length;i++) {
      formObj.elements['aSelectedFiles[]'][i].checked = bSelect;
    }
  }

