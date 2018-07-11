  /*
    these javascript functions are required by the CMS Module CMSModuleChooser
  */
  function CreateModuleInstance(sModuleSpotName, moduleID, view) {
    var formObj = document.getElementsByName('moduleblock'+sModuleSpotName)[0];
    formObj.moduleid.value = moduleID;
    formObj.view.value = view;
    formObj.elements['module_fnc['+sModuleSpotName+']'].value = 'NewInstance';
    formObj.submit();
  }

  function ClearModuleInstance(sModuleSpotName) {
    var formObj = document.getElementsByName('moduleblock'+sModuleSpotName)[0];
    formObj.elements['module_fnc['+sModuleSpotName+']'].value = 'ClearInstance';
    formObj.submit();
  }

  function DeleteModuleInstance(sModuleSpotName) {
    if (confirm(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.confirm_module_content_delete'))) {
      var formObj = document.getElementsByName('moduleblock'+sModuleSpotName)[0];
      formObj.elements['module_fnc['+sModuleSpotName+']'].value = 'DeleteInstance';
      formObj.submit();
    }
  }

  function CopyModuleInstance(sModuleSpotName) {
      var formObj = document.getElementsByName('moduleblock'+sModuleSpotName)[0];
      formObj.elements['module_fnc['+sModuleSpotName+']'].value = 'CopyInstance';
      formObj.submit();
  }

  function ChangeView(sModuleSpotName,view) {
    var formObj = document.getElementsByName('moduleblock'+sModuleSpotName)[0];
    formObj.elements['module_fnc['+sModuleSpotName+']'].value = 'ChangeView';
    formObj.view.value = view;
    formObj.submit();
  }

  function EditTable(tableID,moduleInstanceID, sessionid) {
    window.open("cms?pagedef=tablemanager&sRestrictionField=cms_tpl_module_instance_id&sRestriction="+moduleInstanceID+"&id="+tableID,"_top","");
  }

  function Rename(sModuleSpotName, instanceName) {
    var vNewName = '';
    var defaultValue = instanceName;
    vNewName = window.prompt('Bitte geben Sie einen neuen Namen an:',defaultValue);
    if (vNewName != null && vNewName != defaultValue) {
      var formObj = document.getElementsByName('moduleblock'+sModuleSpotName)[0];
      formObj.instancename.value = vNewName;
      formObj.elements['module_fnc['+sModuleSpotName+']'].value = 'RenameInstance';
      formObj.submit();
    }
  }

  function LoadModuleInstance(sModuleSpotName, pageid,sessionid) {
    document.location.href= '/cms?pagedef=templateengineplain&_mode=load_module&id='+pageid+'&spotname='+sModuleSpotName;
  }

  function LoadModuleInstanceCopy(sModuleSpotName, pageid,sessionid) {
    document.location.href = '/cms?pagedef=templateengineplain&_mode=load_module&id='+pageid+'&spotname='+sModuleSpotName+'&bLoadCopy=1';
  }

  function SwitchModuleInstances(sModuleSpotName,sTargetModuleSpotName) {
    var formObj = document.forms['moduleblock'+sModuleSpotName];
    formObj.elements['module_fnc['+sModuleSpotName+']'].value = 'SwitchInstances';
    var targetfield = document.createElement("input");
    targetfield.setAttribute("type","hidden");
    targetfield.setAttribute("value",sTargetModuleSpotName);
    targetfield.setAttribute("name","sTargetModuleSpotName");
    formObj.appendChild(targetfield);
    formObj.submit();
  }

  function EditCmsMasterSpot(controller,tableid,id) {
    top.window.location.href = controller+'?pagedef=tableeditor&tableid='+tableid+'&id='+id;
  }

  /* esono tree menu */

  var treeMenuHeight = 260;

  var treeMenuHeaderHeight = 20;

  var lastTopMainMenu = 0;

  function scrollDown() {
      var scrollObject = $('#menuWrapper').find('li.active').last().find("ul").first();
      var calcTreeMenuHeight = treeMenuHeight - (scrollObject.parents("li.active").length-1)*treeMenuHeaderHeight;
      scrollObject.siblings("a").height();
      scrollObject.css("position","relative");
      var actPos = parseInt(scrollObject.css("top"))
      var actHeight = parseInt(scrollObject.css("height"))
      if(actHeight + actPos - calcTreeMenuHeight > 0){
       var newpos = actPos - calcTreeMenuHeight;
       scrollObject.animate({ top: newpos+"px"}, 100);
      }
  }

  function scrollUp() {
      var scrollObject = $('#menuWrapper').find('li.active').last().find("ul").first();
      var calcTreeMenuHeight = treeMenuHeight - (scrollObject.parents("li.active").length-1)*treeMenuHeaderHeight;
      scrollObject.css("position","relative");
      var actPos = parseInt(scrollObject.css("top"))
      if(actPos <= -calcTreeMenuHeight || actPos < 0){
          var newpos =  actPos + calcTreeMenuHeight;
          if(actPos < 0 && actPos > calcTreeMenuHeight){
              var newpos =  0;
          }
          scrollObject.animate({ top: newpos+"px"}, 100);
      }
  }

  function openMenuLevel(element) {
    // hide all levels apart from the current node and show the siblings
    if($(element).parent('li').attr("class") == 'active') {
      $(element).parent('li').parent('ul').children('li').not(".active").css({ 'display': 'block' });
      $(element).parent('li').removeClass('active');
      $(element).removeClass('expanded');
      var scrollObject = $('#menuWrapper').find('li.active').first().find("ul").first();
      scrollObject.css("top",lastTopMainMenu+"px");
    } else {
      // set the current node active and hide all siblings
      $(element).parent('li').addClass('active');
      $(element).addClass('expanded');
      $(element).parent('li').parent('ul').children('li').not(".active").css({ 'display': 'none' });
      $(element).siblings("ul").css("top","0px");
      var scrollObject = $('#menuWrapper').find('li.active').first().find("ul").first();
      lastTopMainMenu = parseInt(scrollObject.css("top"));
      scrollObject.css("top","0px");
    }
  }

  function initModuleChooser($) {
    $("div.CMSModuleChooserCrosshair").draggable(
      {
        zIndex: 99999,
        revert: true,
        iframeFix: true,
        helper: "clone",
        appendTo: "body",
        start: function(event, ui) {
          $(event.target).parent().parent().droppable('disable');
        },
        stop: function(event, ui) {
          $(event.target).parent().parent().droppable('enable');
        }
      }
    );

    $("div.CMSModuleChooserTarget").droppable(
      {
        accept : ".CMSModuleChooserCrosshair",
        tolerance: "intersect",
        drop: function (event, ui)
          {
            var targetspot = $(this).data("spotname");
            var sourcemodulespot = $(ui.draggable).data("spotname");
            SwitchModuleInstances(sourcemodulespot,targetspot);
          },
        over: function (event, ui)
        {
          $(this).find(".cmsModuleMenuLauncher").parent().parent().css("border","2px red solid")
        },
        out: function (event, ui)
        {
          $(this).find(".cmsModuleMenuLauncher").parent().parent().css("border","2px black solid")
        }
      }
    );
  }
