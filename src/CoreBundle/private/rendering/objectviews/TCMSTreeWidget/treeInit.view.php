<script type="text/javascript">
    $(document).ready(function () {

        /**
         * init the jsTree widget
         */
        $("#treePlacer").jstree({
            "plugins":[ "themes", "json_data", "cookies", "ui", "crrm", "dnd", <?php if (!empty($sContextMenuItems)) {
                echo '"contextmenu",';
            } ?> ],

            "core":{
                "animation":0,
                "html_titles":false
            },
            "theme":'default',
            "themes":{
                "theme":"default",
                "dots":true,
                "icons":false
            },

            "json_data":{
                "ajax":{
                    // the URL to fetch the data
                    "url":"<?php echo PATH_CMS_CONTROLLER; ?>?" + _cmsauthenticitytoken_parameter,
                    // this function is executed in the instance's scope (this refers to the tree instance)
                    // the parameter is the node being loaded (may be -1, 0, or undefined when loading the root nodes)
                    "data":function (n) {
                        // the result is fed to the AJAX request `data` option
                        return {
                            "pagedef":'<?php echo $sAjaxPageDef; ?>',
                            "module_fnc[<?php echo $sAjaxModuleSpot; ?>]":'ExecuteAjaxCall',
                            "_fnc":'GetChildren',
                            "id":n.attr ? n.attr("id").replace("node", "") : 1
                        };
                    }
                }
            },

            "dnd":{
                "drop_target":false,
                "drag_target":false
            },

            "crrm":{
                "move":{
                    "check_move":function (m) {
                        var returnVal = true;
                        var p = this._get_parent(m.o); // prevent move of root element
                        if (!p) returnVal = false;
                        return returnVal;
                    }
                }
            }<?php
                    if (!empty($sContextMenuItems)) {
                        ?>,

            "contextmenu":{
                select_node:true,
                items: <?php echo $sContextMenuItems; ?>
            }
            <?php
                    }
            ?>
        })

            .bind("move_node.jstree", function (e, data) {
                data.rslt.o.each(function (i) {
                    $.ajax({
                        async:false,
                        cache:false,
                        dataType:'json',
                        type:'POST',
                        url:"<?php echo PATH_CMS_CONTROLLER; ?>?" + _cmsauthenticitytoken_parameter,
                        data:{
                            "pagedef":'<?php echo $sAjaxPageDef; ?>',
                            "module_fnc[<?php echo $sAjaxModuleSpot; ?>]":'ExecuteAjaxCall',
                            "_fnc":'MoveNode',
                            "sourceNodeID":$(this).attr("id").replace("node", ""),
                            "targetNodeID":data.rslt.np.attr("id").replace("node", ""),
                            "position":data.rslt.cp + i,
                            "title":data.rslt.name,
                            "copy":data.rslt.cy ? 1 : 0
                        },
                        success:function (r) {
                            if (!r.status) {
                                $.jstree.rollback(data.rlbk); // rollback the move
                            } else {
                                $(data.rslt.oc).attr("id", "node" + r.id);
                                if (data.rslt.cy && $(data.rslt.oc).children("UL").length) {
                                    data.inst.refresh(data.inst._get_parent(data.rslt.oc));
                                }
                            }
                        }
                    });
                });
            })

            .bind("rename.jstree", function (e, data) {
                $.ajax({
                    async:false,
                    cache:false,
                    dataType:'json',
                    type:'POST',
                    url:"<?php echo PATH_CMS_CONTROLLER; ?>?" + _cmsauthenticitytoken_parameter,
                    data:{
                        "pagedef":'<?php echo $sAjaxPageDef; ?>',
                        "module_fnc[<?php echo $sAjaxModuleSpot; ?>]":'ExecuteAjaxCall',
                        "_fnc":'RenameNode',
                        "sNodeID":data.rslt.obj.attr("id").replace("node", ""),
                        "sNewTitle":data.rslt.new_name
                    },
                    success:function (r) {
                        if (!r.status) {
                            $.jstree.rollback(data.rlbk);
                        } else {

                        }
                    }
                });
            })

            .bind("delete_node.jstree", function (e, data) {
                if (confirm('<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_tree_widget.confirm_delete_folder')); ?>')) {
                    $.ajax({
                        async:false,
                        cache:false,
                        dataType:'json',
                        type:'POST',
                        url:"<?php echo PATH_CMS_CONTROLLER; ?>?" + _cmsauthenticitytoken_parameter,
                        data:{
                            "pagedef":'<?php echo $sAjaxPageDef; ?>',
                            "module_fnc[<?php echo $sAjaxModuleSpot; ?>]":'ExecuteAjaxCall',
                            "_fnc":'CheckDirItemsConnectionsAndDelete',
                            "sNodeID":data.rslt.obj.attr("id").replace("node", "")
                        },
                        success:function (r) {
                            if (!r) {
                                $.jstree.rollback(data.rlbk);
                                $('#' + data.rslt.obj.attr("id").replace("node", "") + ' a').click();
                            } else {
                                if (r instanceof Array) {
                                    if (r.length == 2) {
                                        $.jstree.rollback(data.rlbk);
                                        $("#treePlacer").jstree("refresh", r[0]);
                                        _connectedDataHTML = r;
                                        ShowConnectionDialog(1);
                                    }
                                } else {
                                    showFileList(<?php echo $sRootNodeID; ?>);
                                }
                            }
                        }
                    });
                } else {
                    $.jstree.rollback(data.rlbk);
                    // $('#'+data.rslt.obj.attr("id").replace("node","") + ' a').click();
                }
            })

            .bind("select_node.jstree", function (e, data) {
                showFileList(data.rslt.obj.attr("id").replace("node", ""));
            });
    });

    /**
     * creates a jsTree node
     * @param recordData
     */
    function CreateTreeNode(recordData) {
        var parentNodeID = 'node' + recordData.parentID;

        $("#treePlacer").jstree("refresh", parentNodeID);
        CloseModalIFrameDialog();
    }

</script>
