function (NODE)
{
var obj = {};

var sNodeType = '';
if(NODE.attr('rel').indexOf('rootNode') != -1) sNodeType = 'rootNode';

var bHasNewPermission = false;
if(NODE.attr('rel').indexOf('bHasNewPermission') != -1) bHasNewPermission = true;

var bHasEditPermission = false;
if(NODE.attr('rel').indexOf('bHasEditPermission') != -1) bHasEditPermission = true;

var bHasDeletePermission = false;
if(NODE.attr('rel').indexOf('bHasDeletePermission') != -1) bHasDeletePermission = true;

var bHasUploadPermission = false;
if(NODE.attr('rel').indexOf('bHasUploadPermission') != -1) bHasUploadPermission = true;

if(bHasNewPermission) {
obj['create'] = {
"label"             : "<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_tree_widget.action_new_node')); ?>",
"action"            : function (obj) { CreateNode(obj); },
"separator_before"  : false,
"separator_after"   : true,
"icon"              : 'fas fa-plus'
}
}

if (sNodeType != 'rootNode') {
if(bHasDeletePermission) {
obj['remove'] = {
"label"             : "<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_tree_widget.action_delete')); ?>",
"action"            : function (obj) { this.remove(obj); },
"separator_before"  : false,
"separator_after"   : true,
"icon"              : 'far fa-trash-alt'
}
}

if(bHasEditPermission) {
obj['ccp'] = {
"separator_before"    : true,
"icon"        : 'fas fa-sign-out-alt',
"separator_after"    : false,
"label"                : "<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_tree_widget.action_move')); ?>",
"action"            : false,
"submenu" : {
"copy" : false,
"cut" : {
"separator_before"    : false,
"separator_after"    : false,
"label"                : "<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_tree_widget.action_cut')); ?>",
"icon"        : 'fas fa-cut',
"action"            : function (obj) { this.cut(obj); }
},
"paste" : {
"separator_before"    : false,
"icon"                : false,
"separator_after"    : false,
"label"                : "<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_tree_widget.action_paste')); ?>",
"icon"        : 'fas fa-paste',
"action"            : function (obj) { this.paste(obj); }
}
}
}
}

if(bHasUploadPermission) {
obj['upload'] = {
"label"             : "<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_tree_widget.action_upload')); ?>",
"action"            : function (obj) {
var sNodeID = obj.attr("id").replace("node","");
UploadFiles(sNodeID);
},
"separator_before"  : true,
"separator_after"   : false,
"icon"              : 'fas fa-upload'
}

obj['uploadlocal'] = {
"label"             : "<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_tree_widget.action_import_ftp')); ?>",
"action"            : function (obj) {
var sNodeID = obj.attr("id").replace("node","");
UploadFilesFromLocal(sNodeID);
},
"separator_before"  : false,
"separator_after"   : false,
"icon"              : 'fas fa-file-import'
}
}

if(bHasEditPermission) {
obj['pastefiles'] = {
"label"             : "<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_tree_widget.action_move_to_here')); ?>",
"action"            : function (obj) {
var sNodeID = obj.attr("id").replace("node","");
PasteFiles(sNodeID);
},
"separator_before"  : false,
"separator_after"   : true,
"icon"              : 'fas fa-sign-in-alt'
}

}

}
if(bHasEditPermission) {
obj['rename'] = {
// The item label
"label"             : "<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_tree_widget.action_rename')); ?>",
// The function to execute upon a click
"action"            : function (obj) { this.rename(obj); },
// All below are optional
// "_disabled"         : true,     // clicking the item won't do a thing
// "_class"            : "class",  // class is applied to the item LI node
"separator_before"  : false,
"separator_after"   : true,
// false or string - if does not contain `/` - used as classname
"icon"              : 'fas fa-edit'
}
}

return obj;
}

