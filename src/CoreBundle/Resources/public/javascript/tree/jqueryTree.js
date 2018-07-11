var simpleTreeCollection;

$(document).ready(function(){
	simpleTreeCollection = $('.simpleTree').simpleTree({
		autoclose: false,
		afterClick:function(node){
			// alert("text-"+$('span:first',node).text());
		},
		afterDblClick:function(node) {
		  openPageEditor(node);
		},
		afterMove:function(destination, source, pos){
		  moveNode(source.attr('esrealid'),destination.attr('esrealid'),pos);
			// alert("child of: "+destination.attr('esrealid')+" moved item: "+source.attr('esrealid')+" pos: "+pos);
		},
		afterAjax:function() {
			BindContextMenu();
		},
		animate:false,
		afterContextMenu:function(node) {
		  $('span:first',node).click();
		},
		docToFolderConvert:true
	});

});
