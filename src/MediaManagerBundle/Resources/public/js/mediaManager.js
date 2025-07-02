;(function ($, window, document, undefined) {

    "use strict";

    var pluginName = "chameleonSystemMediaManager",
        defaults = {
            urls: {
                listUrl: '',
                editMediaTreePropertiesUrlTemplate: '',
                mediaTreeNodeInfoUrlTemplate: '',
                mediaTreeNodeInsertUrl: '',
                mediaTreeNodeRenameUrl: '',
                mediaTreeNodeDeleteUrl: '',
                mediaTreeNodeMoveUrl: '',
                mediaItemDeleteConfirmationUrl: '',
                mediaItemDeleteUrl: '',
                mediaItemDetailsUrlTemplate: '',
                imagesMoveUrl: '',
                quickEditUrl: '',
                uploaderUrlTemplate: '',
                autoCompleteSearchUrl: '',
                uploaderReplaceMediaItemUrl: '',
                postSelectUrl: '',
                mediaItemFindUsagesUrl: ''
            },
            startDraggingDistance: 20,
            splitSizes: [20, 80],
            accessRightsMedia: null,
            accessRightsMediaTree: null,
            activeMediaItemId: null
        },
        stateDefaults = {
            pageNumber: 0,
            pageSize: 0,
            searchTerm: '',
            mediaTreeNodeId: '',
            listView: '',
            showSubtree: false,
            deleteWithUsageSearch: false,
            sortColumn: null,
            pickImageMode: false,
            pickImageCallback: '',
            parentIFrame: '',
            pickImageWithCrop: false
        };

    function Plugin(element, state, options) {
        this.element = $(element);
        this.settings = $.extend({}, defaults, options);
        this.state = $.extend({}, stateDefaults, state);
        this._defaults = defaults;
        this._name = pluginName;
        this.editContainer = null;
        this.draggingData = null;
        this.splitInstance = null;
        this.lastDetailPageShown = null;
        this.init();
    }

    $.extend(Plugin.prototype, {
        init: function () {
            var element = $(this.element);
            this.treeContainer = element.find('.tree-container');
            this.editContainer = element.find('.edit-container');
            this.initTree();
            this.resetSplit();
        },
        initTree: function () {
            var self = this;
            $(this.treeContainer).jstree({
                'plugins': [
                    'dnd', 'contextmenu', 'state', 'wholerow', 'types'
                ],
                'core': {
                    'check_callback': true,
                    'multiple': true},
                'dnd': {
                    'is_draggable': function (node) {
                        return !!self.settings.accessRightsMediaTree.edit;
                    }
                },
                'types': {
                    'default': {
                        'icon': 'far fa-folder'
                    }
                },
                'contextmenu': {
                    select_node: false,
                    'items': function (node) {

                        var contextMenu = {};

                        if (self.settings.accessRightsMediaTree.new) {
                            contextMenu.createItem = {
                                label: CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.tree_context_menu.new_folder'),
                                icon: "fas fa-folder-open",
                                action: function (data) {

                                    var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference),
                                        newFolderName = CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.tree.new_folder');

                                    $.ajax({
                                        type: "POST",
                                        async: true,
                                        data: {
                                            'parentId': encodeURIComponent(obj.id.replace("mediaTreeNode", "")),
                                            'name': newFolderName
                                        },
                                        url: self.settings.urls.mediaTreeNodeInsertUrl,
                                        error: function (responseData) {
                                            data.instance.refresh();
                                            self.showErrorFromAjaxResponse(responseData);
                                        },
                                        success: function (jsonData) {
                                            var oNewNode = {
                                                "id": 'mediaTreeNode' + jsonData.id,
                                                "text": newFolderName,
                                                icon: 'jstree-folder',
                                                a_attr: {href: jsonData.newurl}
                                            };
                                            inst.create_node(obj, oNewNode, "last", function (new_node) {
                                                setTimeout(function () {
                                                    inst.edit(new_node);
                                                }, 0);
                                            });
                                        },
                                        dataType: 'JSON'
                                    });
                                }
                            };
                        }

                        if (self.settings.accessRightsMediaTree.edit) {
                            contextMenu.renameItem = {
                                label: CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.tree_context_menu.rename'),
                                icon: "fas fa-edit",
                                action: function (data) {
                                    var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                    var ref = self.treeContainer.jstree(true);
                                    ref.edit(obj);
                                }
                            };
                        }

                        if (self.settings.accessRightsMediaTree.delete) {
                            contextMenu.deleteItem = {
                                label: CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.tree_context_menu.delete_folder'),
                                icon: "fas fa-trash-alt text-danger",
                                action: function (data) {
                                    var confirmation = window.confirm(CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.delete.folder_are_you_sure')+"\n"+data.reference.text());
                                    if (false === confirmation) {
                                        return;
                                    }

                                    var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                    var ref = self.treeContainer.jstree(true);
                                    ref.delete_node(obj);
                                },
                                separator_after: true
                            };
                        }

                        if (self.settings.accessRightsMedia.new) {
                            contextMenu.upload = {
                                label: CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.tree_context_menu.upload'),
                                icon: "fas fa-upload",
                                action: function (data) {
                                    var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                    var ref = self.treeContainer.jstree(true);
                                    self.openIframeLayover('Upload', self.settings.urls.uploaderUrlTemplate.replace('--id--', obj.id.replace('mediaTreeNode', '')));
                                },
                                separator_after: true
                            };
                        }

                        if (self.settings.accessRightsMediaTree.edit) {
                            contextMenu.properties = {
                                label: CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.tree_context_menu.edit_properties'),
                                icon: "fas fa-cog",
                                action: function (data) {
                                    var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);


                                    var id = obj.id.replace("mediaTreeNode", "");
                                    var url = self.settings.urls.editMediaTreePropertiesUrlTemplate.replace('--id--', id);

                                    self.openIframeLayover(obj.text, url, $(window).height());
                                    //kind of a hack to update tree on an ajax save in iframe, which will call parent.CreateTreeNode()
                                    window.CreateTreeNode = function (recordData) {
                                        self.updateTreeElement(recordData.id);
                                    };


                                }
                            };
                        }

                        return contextMenu;
                    }
                }
            })
                .on('create_node.jstree', self.onTreeCreate.bind(this))
                .on('rename_node.jstree', self.onTreeRename.bind(this))
                .on('move_node.jstree', self.onTreeMove.bind(this))
                .on('delete_node.jstree', self.onTreeDelete.bind(this))
                .on('changed.jstree', self.onTreeChange.bind(this))
                .one('state_ready.jstree', self.onTreeRestoredState.bind(this))
            ;

            $(window).on('resize', function () {
                $('.gutter-horizontal', self.element).css('height', self.element.height());
            });

            $('.media-search-box').on('submit', function (evt) {
                var input = $(this).find('.search-all');

                var state = {
                    mediaTreeNodeId: '1',
                    pageNumber: 0,
                    searchTerm: input.val()
                };
                self.updateListView(state);
                input.val('');
                evt.preventDefault();
            });
        },
        onTreeCreate: function (e, data) {
        },
        onTreeChange: function (e, data) {
            if (data && data.selected && data.selected.length) {
                if (data.selected.length > 1) {
                    this.editContainer.html('<div class="multiple-selected">' + CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.tree.multiple_selected_message') + '</div>' + '<div class="delete-multiple-selected">' + CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.tree.delete_multiple_selected') + '</div>');
                    this.editContainer.find('.delete-multiple-selected').off('click');
                    var self = this;
                    this.editContainer.find('.delete-multiple-selected').on('click', function (e) {
                        e.preventDefault();
                        $.each(data.selected, function (index, id) {
                            var ref = self.treeContainer.jstree(true);
                            var obj = ref.get_node(id);
                            ref.delete_node(obj);
                        });
                    });
                } else {
                    var id = data.selected[0].replace("mediaTreeNode", "");
                    var state = {
                        'mediaTreeNodeId': id,
                        'pageNumber': 0
                    };
                    this.updateListView(state);
                }
            }
            else {
                this.editContainer.html(CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.tree.nothing_selected_message'));
            }
        },
        onTreeRename: function (e, data) {
            var self = this;
            $.ajax({
                type: "POST",
                async: true,
                data: {
                    'id': encodeURIComponent(data.node.id.replace("mediaTreeNode", "")),
                    'name': data.text
                },
                url: self.settings.urls.mediaTreeNodeRenameUrl,
                error: function (responseData) {
                    data.instance.refresh();
                    self.showErrorFromAjaxResponse(responseData)
                },
                dataType: 'JSON'
            });
        },
        onTreeMove: function (e, data) {
            var self = this;
            $.ajax({
                async: true,
                type: 'POST',
                url: self.settings.urls.mediaTreeNodeMoveUrl,
                data: {
                    "id": data.node.id.replace("mediaTreeNode", ""),
                    "parentId": data.node.parent.replace("mediaTreeNode", ""),
                    "position": data.position
                },
                error: function (responseData) {
                    data.instance.refresh();
                    self.showErrorFromAjaxResponse(responseData);
                }
            });
        },
        onTreeDelete: function (e, data) {
            var id = data.node.id.replace("mediaTreeNode", "");
            var self = this;
            CHAMELEON.CORE.showProcessingModal();
            $.ajax({
                type: "POST",
                async: true,
                url: self.settings.urls.mediaTreeNodeDeleteUrl,
                data: {
                    'id': encodeURIComponent(id)
                },
                error: function (responseData) {
                    CHAMELEON.CORE.hideProcessingModal();
                    data.instance.refresh();
                    self.showErrorFromAjaxResponse(responseData);
                },
                success: function (jsonData) {
                    CHAMELEON.CORE.hideProcessingModal();
                    self.showSuccessMessage(jsonData);
                    if (self.state.mediaTreeNodeId === id) {
                        self.state.mediaTreeNodeId = null;
                        self.updateListView({});
                    }
                },
                dataType: 'JSON'
            });
        },
        onTreeRestoredState: function () {
            if (this.settings.activeMediaItemId) {
                this.loadDetailPage(this.settings.activeMediaItemId);
                this.settings.activeMediaItemId = null;
            }
        },
        updateListView: function (state) {
            this.showWaitingAnimation();

            var instance = this.treeContainer.jstree(true);
            var id = '1'; //root
            var node = instance.get_selected();
            if (node && node[0] && node.length > 0) {
                id = node[0].replace('mediaTreeNode', '');
            }

            state = this.extendState(state, this.state);
            this.state = state;

            if (state.mediaTreeNodeId !== id) {
                $('.jstree-wholerow-clicked', this.treeContainer).removeClass('jstree-wholerow-clicked');
                $('#mediaTreeNode' + state.mediaTreeNodeId).find('> .jstree-wholerow').addClass('jstree-wholerow-clicked');
            }

            var url = this.settings.urls.listUrl;
            var self = this;
            $.ajax({
                type: "GET",
                async: true,
                url: url,
                data: {
                    'ps': state.pageSize,
                    'p': state.pageNumber,
                    's': state.searchTerm,
                    'mediaTreeId': state.mediaTreeNodeId,
                    'listView': state.listView,
                    'subtree': state.showSubtree ? '1' : '0',
                    'enableUsageSearch': state.deleteWithUsageSearch ? '1' : '0',
                    'sr': state.sortColumn,
                    'pickImage': state.pickImageMode ? '1' : '0',
                    'pickImageCallback': state.pickImageMode ? state.pickImageCallback : null,
                    'parentIFrame': state.parentIFrame ? state.parentIFrame : null,
                    'pickImageWithCrop': state.pickImageMode && state.pickImageWithCrop ? '1' : '0'
                },
                error: function (responseData) {
                    instance.deselect_all();
                    self.showErrorFromAjaxResponse();
                },
                success: function (jsonData) {
                    self.renewEditContainer();
                    self.editContainer.html(jsonData.contentHtml);
                    self.listViewUpdated();
                },
                dataType: 'JSON'
            });
        },
        extendState: function (state, defaults) {
            if (typeof state.mediaTreeNodeId === 'undefined' || null === state.mediaTreeNodeId) {
                state.mediaTreeNodeId = defaults.mediaTreeNodeId;
            }
            if (typeof state.pageNumber === 'undefined' || null === state.pageNumber) {
                state.pageNumber = defaults.pageNumber;
            }
            if (typeof state.pageSize === 'undefined' || null === state.pageSize) {
                state.pageSize = defaults.pageSize;
            }
            if (typeof state.searchTerm === 'undefined' || null === state.searchTerm) {
                state.searchTerm = defaults.searchTerm;
            }
            if (typeof state.listView === 'undefined' || null === state.listView) {
                state.listView = defaults.listView;
            }
            if (typeof state.showSubtree === 'undefined' || null === state.showSubtree) {
                state.showSubtree = defaults.showSubtree;
            }
            if (typeof state.deleteWithUsageSearch === 'undefined' || null === state.deleteWithUsageSearch) {
                state.deleteWithUsageSearch = defaults.deleteWithUsageSearch;
            }
            if (typeof state.sortColumn === 'undefined' || null === state.sortColumn) {
                state.sortColumn = defaults.sortColumn;
            }
            if (typeof state.pickImageMode === 'undefined' || null === state.pickImageMode) {
                state.pickImageMode = defaults.pickImageMode;
            }
            if (typeof state.pickImageWithCrop === 'undefined' || null === state.pickImageWithCrop) {
                state.pickImageWithCrop = defaults.pickImageWithCrop;
            }
            if (typeof state.pickImageCallback === 'undefined' || null === state.pickImageCallback) {
                state.pickImageCallback = defaults.pickImageCallback;
            }
            if (typeof state.parentIFrame === 'undefined' || null === state.parentIFrame) {
                state.parentIFrame = defaults.parentIFrame;
            }

            return state;
        },
        listViewUpdated: function () {
            var self = this;
            var frameInEditor = $('iframe', self.editContainer);
            if (frameInEditor.length > 0) {
                frameInEditor.on('load', function () {
                    frameInEditor.css({
                        width: frameInEditor[0].contentWindow.document.body.scrollWidth,
                        height: frameInEditor[0].contentWindow.document.body.scrollHeight,
                    });
                    self.editContainer.css('height', 'auto');
                });
            }

            self.resetSplit();

            $("html, body").animate({
                scrollTop: 0
            }, "fast");

            self.attachXSelected();

            $('.edit-media-tree-node', self.editContainer).on('click', function (evt) {
                var url = $(this).attr('href');
                var id = $(this).data('id');
                var iframe = self.openIframeLayover($(this).parent('.headline').text(), url, $(window).height());
                //kind of a hack, to update tree on ajax save in iframe
                window.CreateTreeNode = function (recordData) {
                    self.updateTreeElement(recordData.id);
                };

                evt.preventDefault();
            });

            $('.action-bar .upload', self.editContainer).on('click', function (evt) {
                var id = $(this).data('id');
                self.openIframeLayover('Upload', self.settings.urls.uploaderUrlTemplate.replace('--id--', id));
                evt.preventDefault();
            });

            $('.action-bar .delete', self.editContainer).on('click', function (evt) {
                self.confirmDeleteForMediaItemIds(self.getSelectedImageIds());
                evt.preventDefault();
            });

            $('.delete-item', self.editContainer).on('click', function (evt) {
                self.confirmDeleteForMediaItemIds([$(this).parents('.cms-media-item').data('id')]);
                evt.preventDefault();
            });

            $('.show-details', self.editContainer).on('click', function (evt) {
                var mediaItemContainer = $(this).parents('.cms-media-item');
                self.loadDetailPage(mediaItemContainer.data('id'), mediaItemContainer.data('name'));
                evt.preventDefault();
            });

            $('.pick-image', self.editContainer).on('click', function (evt) {
                if ('' !== self.state.parentIFrame) {
                    var parentIframeElement = parent.document.getElementById(self.state.parentIFrame);
                }

                var mediaItemContainer = $(this).parents('.cms-media-item');

                $.ajax({
                    type: "POST",
                    async: true,
                    url: self.settings.urls.postSelectUrl,
                    data: {
                        'mediaItemId': mediaItemContainer.data('id')
                    },
                    error: function (responseData) {
                        self.showErrorFromAjaxResponse();
                    },
                    success: function (jsonData) {
                        if (parentIframeElement && parentIframeElement.contentWindow) {
                            eval('parentIframeElement.contentWindow.' + self.sanitizeCallbackFunctionName(self.state.pickImageCallback) + '("' + mediaItemContainer.data('id') + '")');
                        } else {
                            eval(self.sanitizeCallbackFunctionName(self.state.pickImageCallback) + '("' + mediaItemContainer.data('id') + '")');
                        }
                    },
                    dataType: 'JSON'
                });

                evt.preventDefault();
            });

            $('.go-to-page', self.editContainer).on('click', function (evt) {
                if (false === $(this).parent().hasClass('disabled')) {
                    var pageNumber = $(this).data('page-number');
                    var state = {
                        pageNumber: pageNumber
                    };
                    self.updateListView(state);
                }
                evt.preventDefault();
            });

            $('.page-size-selection', self.editContainer).on('change', function (evt) {
                var state = {
                    pageNumber: 0,
                    pageSize: $(this).val()
                };
                self.updateListView(state);
            });

            $('.search-box', self.editContainer).on('submit', function (evt) {
                var state = {
                    pageNumber: 0,
                    searchTerm: $(this).find('.search-category').val()
                };
                self.updateListView(state);
                evt.preventDefault();
            });

            $('.list-view', self.editContainer).on('click', function (evt) {
                var view = $(this).data('view');
                var state = {
                    listView: view
                };
                self.updateListView(state);
            });

            $('.show-subtree', self.editContainer).on('change', function (evt) {
                var showTree = false;
                if ($(this).is(':checked')) {
                    showTree = true;
                }
                var state = {
                    pageNumber: 0,
                    showSubtree: showTree
                };
                self.updateListView(state);
            });

            $('.delete-with-usage-search', self.editContainer).on('change', function (evt) {
                var enableUsageSearch = false;
                if ($(this).is(':checked')) {
                    enableUsageSearch = true;
                }
                var state = {
                    deleteWithUsageSearch: enableUsageSearch
                };
                self.updateListView(state);
            });

            $('.sort-order', self.editContainer).on('change', function (evt) {
                var state = {
                    pageNumber: 0,
                    sortColumn: $("option:selected", this).val()
                };
                self.updateListView(state);
            });

            $('.editable').editable(self.settings.urls.quickEditUrl, {
                onblur: 'submit',
                tooltip: CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.editable.tooltip'),
                placeholder: CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.editable.tooltip'),
                submitdata: function (value, settings) {
                    return {type: $(this).data('type'), mediaItemId: $(this).parents('.cms-media-item').data('id')};
                }
            });

            $('[data-select2-option]').each(function () {
                $(this).select2($(this).data('select2-option'));
            });

            self.handleAutoCompleteOnSearchFields();

        },
        renewEditContainer: function () {
            var self = this;

            var originalClass = self.editContainer.attr('class');
            var originalId = self.editContainer.attr('id');
            var originalStyle = self.editContainer.attr('style');

            if (self.splitInstance) {
                self.settings.splitSizes = self.splitInstance.getSizes();
                self.splitInstance.destroy();
            }

            //we destroy the container completely to remove all the events
            self.editContainer.remove();
            self.editContainer = null;
            self.editContainer = $('<div class="' + originalClass + '" id="' + originalId + '" style="' + originalStyle + '"></div>');
            self.editContainer.appendTo(self.element);
        },
        resetSplit: function () {
            var sizes = this.settings.splitSizes;
            this.splitInstance = Split(['#tree-container', '#edit-container'], {
                sizes: sizes,
                minSize: 300
            });

            $('.gutter-horizontal', this.element).css('height', $(this.element).height());
        },

        sanitizeCallbackFunctionName: function (functionAsString) {
            var sanitizedFunctionName = functionAsString.replace(/[^a-z0-9\.\_]/gi, '');
            if (sanitizedFunctionName !== functionAsString) {
                console.log('prohibited characters in callback function, please fix.');
            }

            return sanitizedFunctionName;
        },
        loadDetailPage: function (mediaItemId, name) {
            var self = this;
            self.showWaitingAnimation();
            $.ajax({
                type: "GET",
                async: true,
                data: {
                    'pickImage': self.state.pickImageMode ? '1' : '0',
                    'pickImageCallback': self.state.pickImageMode ? self.state.pickImageCallback : null,
                    'parentIFrame': self.state.parentIFrame ? self.state.parentIFrame : null,
                    'pickImageWithCrop': self.state.pickImageMode && self.state.pickImageWithCrop ? '1' : '0',
                    'enableUsageSearch': self.state.deleteWithUsageSearch ? '1' : '0',
                },
                url: self.settings.urls.mediaItemDetailsUrlTemplate.replace('--id--', mediaItemId),
                error: function (responseData) {
                    self.showErrorFromAjaxResponse(responseData);
                },
                success: function (jsonData) {
                    if (typeof name === 'undefined') {
                        name = jsonData.mediaItemName
                    }
                    self.openLayover(name, jsonData.contentHtml);
                    self.detailsShown();

                    $(".entry-id-copy-button").on("click", function() {
                        CHAMELEON.CORE.copyToClipboard($(this).data("entry-id"));
                    });

                    $(".image-url-copy-button").on("click", function() {
                        CHAMELEON.CORE.copyToClipboard($(this).data("image-url"));
                    });
                },
                dataType: 'JSON'
            });
        },
        uploadCompleteCallback: function () {
            this.updateListView({});
        },
        showWaitingAnimation: function () {
            $('<div class="loading-animation"><img src="/bundles/chameleonsystemmediamanager/img/loading.svg" alt=""></div>').appendTo(this.editContainer);
        },
        hideWaitingAnimation: function () {
            $('.loading-animation', this.editContainer).remove();
        },
        confirmDeleteForMediaItemIds: function (ids) {
            var self = this;
            self.showWaitingAnimation();
            var enableUsageSearch = '0';

            if ($('.delete-with-usage-search').is(':checked') || true === self.state.deleteWithUsageSearch) {
                enableUsageSearch = '1';
            }
            $.ajax({
                type: "POST",
                async: true,
                url: self.settings.urls.mediaItemDeleteConfirmationUrl,
                data: {
                    'id': ids,
                    'enableUsageSearch': enableUsageSearch
                },
                error: function (data) {
                    self.showErrorFromAjaxResponse(data);
                },
                success: function (jsonData) {
                    var layover = self.openLayover(CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.delete.are_you_sure'), jsonData.contentHtml);
                    layover.find('.revoke-delete').on('click', function (evt) {
                        if ($(this).parents('.items-to-delete').find('.item-to-delete').length > 1) {
                            $(this).parents('.item-to-delete').remove();
                        } else {
                            layover.close();
                        }

                        evt.preventDefault();
                    });
                    layover.find('.cancel-delete').on('click', function (evt) {
                        layover.close();
                        evt.preventDefault();
                    });
                    layover.find('.confirm-delete').on('click', function (evt) {

                        var idsToDelete = $(this).parents('.items-to-delete').find('.item-to-delete').map(function () {
                            return $(this).data('id');
                        }).get();

                        $.ajax({
                            type: "POST",
                            async: true,
                            url: self.settings.urls.mediaItemDeleteUrl,
                            data: {
                                'id': idsToDelete
                            },
                            error: function (data) {
                                self.showErrorFromAjaxResponse(data);
                            },
                            success: function (jsonData) {
                                self.showSuccessMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.delete.success_message'));
                                self.lastDetailPageShown = null;
                                layover.close();
                            },
                            dataType: 'JSON'
                        });

                        evt.preventDefault();
                    });
                },
                dataType: 'JSON'
            });
        },
        showErrorFromAjaxResponse: function (data) {
            this.hideWaitingAnimation();
            var errorMsg = CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.general_error_message');

            if (typeof data !== 'undefined') {
                errorMsg = data.status + " " + data.statusText + "\n";

                try {
                    var jsonData = $.parseJSON(data.responseText);
                    if (jsonData.errorMessage) {
                        errorMsg += jsonData.errorMessage;
                    }
                    if (jsonData.message) {
                        errorMsg += jsonData.message;
                    }
                } catch (err) {
                    // ignore here: is already an error with above general message
                }
            }

            new PNotify({
                text: errorMsg,
                type: 'error'
            });
        },
        showSuccessMessage: function (messageOrJson) {
            this.hideWaitingAnimation();
            var displayMsg = messageOrJson;
            if (messageOrJson.message) {
                displayMsg = messageOrJson.message;
            }
            new PNotify({
                text: displayMsg,
                type: 'success'
            });
        },
        attachXSelected: function () {
            var self = this;

            self.editContainer.off('click');

            self.bindDragging();

            $('.action-bar .select-all', self.editContainer).on('click', function () {
                if ($('.cms-media-item', self.editContainer).not(".xselectable-selected").length > 0) {
                    self.selectAll();
                } else {
                    self.unselect();
                }
            });

            var selectRowCheckboxes = $('.select-row', self.editContainer);

            if (selectRowCheckboxes.length > 0) {
                selectRowCheckboxes.on('change', function () {
                    selectRowCheckboxes.each(function () {
                        if ($(this).is(':checked')) {
                            $(this).parents('.cms-media-item').addClass('xselectable-selected');
                            self.bindDragging();
                        } else {
                            $(this).parents('.cms-media-item').removeClass('xselectable-selected');
                            self.bindDragging();
                        }
                    });
                });

            } else {
                self.editContainer.on('click', function (evt) {
                    if (!evt.ctrlKey && !evt.metaKey && !$(evt.target).is('.cancel-unselect, .cancel-unselect *')) {
                        self.unselect();
                    }
                });

                self.editContainer.xselectable({
                    'filter': '.cms-media-item',
                    'distance': 19,
                    'cancel': ':input,option,.image-container'
                }).on('xselectableselected', function () {
                    self.bindDragging();
                });

                $('.image-container.selectable', self.editContainer).on('click', function (evt) {
                    var selectionParent = $(this).parents('.cms-media-item');
                    var wasSelected = $(this).parents('.cms-media-item').hasClass('xselectable-selected');
                    var numberOfElementsSelected = $('.xselectable-selected', self.editContainer).length;
                    var multipleWereSelected = (numberOfElementsSelected > 1);

                    if (evt.shiftKey) {
                        var selectedElements = $('.xselectable-selected .image-container', self.editContainer);
                        var allElements = $('.image-container', self.editContainer);
                        var clickedIndex = false;
                        if (wasSelected) {
                            clickedIndex = selectedElements.index($(this));
                            selectedElements.each(function (index) {
                                if (index > clickedIndex) {
                                    selectionParent.removeClass('xselectable-selected');
                                }
                            });
                        } else {
                            var startIndex = 0;
                            var endIndex = 0;
                            clickedIndex = allElements.index($(this));
                            if (numberOfElementsSelected >= 1) {
                                var selectedIndex = allElements.index(selectedElements.eq(0));
                                if (clickedIndex > selectedIndex) {
                                    startIndex = selectedIndex + 1;
                                    endIndex = clickedIndex;
                                } else {
                                    startIndex = clickedIndex;
                                    endIndex = selectedIndex - 1;
                                }
                            } else {
                                endIndex = clickedIndex;
                            }
                            var i;
                            for (i = startIndex; i <= endIndex; i++) {
                                allElements.eq(i).parents('.cms-media-item').addClass('xselectable-selected');
                            }
                        }

                        self.bindDragging();

                        evt.stopPropagation();
                        return;
                    }

                    if (!evt.ctrlKey && !evt.metaKey) {
                        self.unselect();
                    }
                    if (!wasSelected) {
                        selectionParent.addClass('xselectable-selected');
                        self.bindDragging();
                    } else {
                        if (!evt.ctrlKey && !evt.metaKey && multipleWereSelected) {
                            selectionParent.addClass('xselectable-selected');
                            self.bindDragging();
                        } else {
                            selectionParent.removeClass('xselectable-selected');
                        }
                    }

                    evt.stopPropagation();
                });
            }
        },
        unselect: function () {
            if ($('.xselectable-selected', this.editContainer).length > 0) {
                $('.xselectable-selected', this.editContainer).removeClass('xselectable-selected');
                $('.select-row', this.editContainer).prop('checked', false);
            }
        },
        selectAll: function () {
            $('.cms-media-item', this.editContainer).addClass('xselectable-selected');
            $('.select-row', this.editContainer).prop('checked', true);
            this.bindDragging();
        },
        bindDragging: function () {
            var self = this;
            this.editContainer.unbind('mousedown.' + 'dragging');
            $(this.editContainer).bind('mousedown.' + 'dragging', function (evt) {
                self.mouseDownStartDragging(evt)
            });
        },
        getSelectedImageIds: function () {
            return $('.xselectable-selected', this.editContainer).map(function () {
                return $(this).data('id');
            }).get();
        },
        mouseDownStartDragging: function (evt) {

            // Do not start selection if it's not done with the left button.
            if (evt.which !== 1) {
                return;
            }

            // Prevent selection from starting on any element matched by
            // or contained within the selector specified by the 'cancel'
            // option.
            var selectorSelection =
                ['.xselectable-selected', '.xselectable-selected' + ' *'].join(',');
            var selectorCanBeDragged =
                ['.cms-media-item', '.cms-media-item' + ' *'].join(',');
            var selectorCannotBeDragged =
                ['.no-drag', '.no-drag' + ' *'].join(',');
            if (!$(evt.target).is(selectorCanBeDragged) || $(evt.target).is(selectorCannotBeDragged)) {
                return;
            }

            var selectionDrag = false;
            if ($(evt.target).is(selectorSelection)) {
                selectionDrag = true;
            }

            // Prevent selection if the mouse is being pressed down on a scrollbar
            // (which is still technically part of the selectable element).
            if (evt.pageX > this.editContainer.offset().left + this.editContainer[0].clientWidth ||
                evt.pageY > this.editContainer.offset().top + this.editContainer[0]) {
                return;
            }

            // Record the initial position of the container, with respect to the
            // document. Also include the current border size (assuming equal
            // top/bottom and right/left border sizes).
            this.draggingData = {};
            this.draggingData.containerDimensions = {
                'top': this.editContainer.offset().top +
                (this.editContainer.outerHeight(false) - this.editContainer.innerHeight()) / 2,
                'left': this.editContainer.offset().left +
                (this.editContainer.outerWidth(false) - this.editContainer.innerWidth()) / 2,
                'width': this.editContainer[0].clientWidth,
                'height': this.editContainer[0].clientHeight
            };

            // Record the initial position of the mouse event, with respect to the
            // document (_not_ including the scrolling position of the selection
            // container).
            this.draggingData.startPosition = {'pageX': evt.pageX, 'pageY': evt.pageY};
            this.draggingData.curPosition = {'pageX': evt.pageX, 'pageY': evt.pageY};

            //add info about dragged elements
            var selectedIds = [];
            if (selectionDrag) {
                selectedIds = this.getSelectedImageIds();
            } else {
                selectedIds = [$(evt.target).parents('.cms-media-item').data('id')];
            }
            this.draggingData.selectedImageIds = selectedIds;
            var namePart = '';
            if (false === selectionDrag) {
                namePart = ' - ' + $(evt.target).parents('.cms-media-item').data('name');
            }
            this.draggingData.elementGhostText = selectedIds.length + namePart + ' ' + CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.dragging.elements');


            // Start listening for mouseup (to terminate selection), movement and
            // wheel scrolling. Mouseups and movement can occur everywhere in the
            // document, if the user moves the mouse outside the selection container.
            this.draggingData.mouseupHandler = $.proxy(this.mouseUpDragging, this);
            $(document).bind('mouseup.' + 'dragging', this.draggingData.mouseupHandler);
            $(document).bind('mousemove.' + 'dragging', $.proxy(this.tickDragging, this));


            // Prevent the default browser dragging to occur.
            evt.preventDefault();

        },
        tickDragging: function (evt, scrollTimestamp) {
            var distance = this.settings.startDraggingDistance;

            // Do nothing if we haven't yet moved past the distance threshold.
            if (!this.draggingData.elementGhost &&
                Math.abs(this.draggingData.startPosition.pageX - evt.pageX) < distance &&
                Math.abs(this.draggingData.startPosition.pageY - evt.pageY) < distance) {

                return;
            }

            if (!this.draggingData.elementGhost) {
                this.draggingData.elementGhost = $(
                    '<div class="media-manager-dragging-ghost"><span class="element-count">' + this.draggingData.elementGhostText + '</span><span class="additions-text"></span></div>').css({
                    'position': 'absolute',
                    top: evt.pageY,
                    left: evt.pageX
                }).appendTo('body');
            } else {
                this.draggingData.elementGhost.css({
                    top: evt.pageY,
                    left: evt.pageX
                });
            }

            if ($(evt.target).is('.jstree-node *')) {
                this.draggingData.elementGhost.find('.additions-text').html(CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.dragging.move_to') + ' ' + $(evt.target).closest('.jstree-node').find('> .jstree-anchor').text()).addClass('has-target');
            } else {
                this.draggingData.elementGhost.find('.additions-text').html(CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.dragging.move_to') + ' ' + CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.dragging.choose_target')).removeClass('has-target');
            }
        },
        mouseUpDragging: function (evt) {

            $('.editable input').trigger('blur');

            if (this.draggingData) {
                this.editContainer.unbind('mousewheel.' + 'dragging');
                $(document).unbind('mousemove.' + 'dragging');
                $(document).unbind('mouseup.' + 'dragging', this.draggingData.mouseupHandler);
                this.draggingData.mouseupHandler = undefined;
                if (this.draggingData.elementGhost && this.draggingData.elementGhost.length > 0) {
                    this.draggingData.elementGhost.remove();
                    if ($(evt.target).is('.jstree-node *')) {
                        var selectedIds = this.draggingData.selectedImageIds;
                        var treeId = $(evt.target).closest('.jstree-node').attr('id').replace('mediaTreeNode', '');

                        var self = this;
                        $.ajax({
                            type: "POST",
                            async: true,
                            url: self.settings.urls.imagesMoveUrl,
                            data: {
                                'treeId': treeId,
                                'imageIds': selectedIds
                            },
                            error: function (data) {
                                self.showErrorFromAjaxResponse(data);
                            },
                            success: function (jsonData) {
                                self.updateListView({});
                                self.showSuccessMessage(jsonData);
                            },
                            dataType: 'JSON'
                        });

                    }
                }
                this.draggingData = null;

            }
        },
        openIframeLayover: function (title, url, height) {
            var self = this;

            self.showWaitingAnimation();

            var layover = self.openLayover(title, '');
            var iframe;

            if (typeof height === 'undefined') {
                iframe = $('<iframe src="' + url + '" scrolling="no" frameborder="0" onload="this.style.height=(this.contentDocument.body.scrollHeight) +\'px\';"></iframe>');
                iframe.css({'width': '100%', 'border': 'none'});
            } else {
                iframe = $('<iframe src="' + url + '"></iframe>');
                iframe.css({'width': '100%', 'height': height});
            }
            iframe.appendTo(layover);

            self.hideWaitingAnimation();
            return iframe;
        },
        openLayover: function (title, html) {
            var self = this;

            //save detail page state
            var detailView = self.editContainer.find('.snippetMediaManagerDetail');
            if (detailView.length === 1) {
                self.lastDetailPageShown = {'id': detailView.data('id'), 'name': detailView.data('name')};
            } else {
                self.lastDetailPageShown = null;
            }

            var closeButton = $('<span class="close-button">' + CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.close_button_text') + ' <i class="fas fa-times"></i></span>');
            var layover = $('<div class="media-manager-layover"><div class="title h3">' + title + '</div></div>');

            closeButton.on('click', function (evt) {
                self.showWaitingAnimation();
                layover.remove();
                self.goBackAfterClosingLayover();
                evt.preventDefault();
            });
            closeButton.appendTo(layover.find('.title'));

            this.renewEditContainer();
            layover.appendTo(this.editContainer);

            this.resetSplit();

            layover.append(html);

            layover.close = function () {
                closeButton.trigger('click');
            };

            return layover;
        },
        goBackAfterClosingLayover: function () {
            if (this.lastDetailPageShown) {
                this.loadDetailPage(this.lastDetailPageShown.id, this.lastDetailPageShown.name);
            } else {
                this.updateListView({});
            }
        },
        updateTreeElement: function (id) {
            //set name and icon from database
            var url = this.settings.urls.mediaTreeNodeInfoUrlTemplate.replace("--id--", id);
            var self = this;
            $.ajax({
                type: "POST",
                async: true,
                url: url,
                error: function (data) {
                    self.showErrorFromAjaxResponse(data);
                },
                success: function (jsonData) {
                    var instance = self.treeContainer.jstree(true);
                    var node = instance.get_node('mediaTreeNode' + jsonData.id);
                    instance.rename_node(node, jsonData.name);
                    if (jsonData.icon) {
                        instance.set_icon(node, jsonData.icon);
                    }
                },
                dataType: 'JSON'
            });
        },
        handleAutoCompleteOnSearchFields: function () {
            var self = this;
            $('.autocomplete-search', this.element).each(function () {
                $(this).off('keyup');
                $(this).off('blur');
                $(this).on('keyup', function (e) {
                    var input = $(this);
                    var term = $(this).val();
                    if (term.length > 1) {
                        $.ajax({
                            type: "GET",
                            async: true,
                            data: {
                                'term': term
                            },
                            url: self.settings.urls.autoCompleteSearchUrl,
                            success: function (jsonData) {
                                var autoCompleteResult = $(jsonData.contentHtml);
                                if ($(autoCompleteResult).find('.record-link').length) {
                                    $('#' + input.data('target')).html(jsonData.contentHtml).show();
                                    $('.record-link', '#' + input.data('target')).on('click', function () {
                                        input.val($(this).text());
                                        $('#' + input.data('target')).hide();
                                        input.parents('form').submit();
                                    });
                                } else {
                                    $('#' + input.data('target')).hide();
                                }
                            },
                            dataType: 'JSON'
                        });
                    }
                }).on('blur', function () {
                    var input = $(this);
                    setTimeout(function () {
                        $('#' + input.data('target')).hide();
                    }, 500);
                });
            });
        },
        detailsShown: function () {
            var self = this;
            $('.replace-image', self.editContainer).on('click', function (evt) {
                var mediaItem = $(this).parents('.cms-media-item');

                //we hack in the correct callback before opening the loader
                window.reloadMediaItemDetail = function () {
                    self.loadDetailPage(mediaItem.data('id'), mediaItem.data('name'));
                };

                self.openIframeLayover(CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.replace_window_title') + ' ' + mediaItem.data('name'), self.settings.urls.uploaderReplaceMediaItemUrl.replace('--id--', mediaItem.data('id')));
                evt.preventDefault();
            });

            $('.create-editor', self.editContainer).on('click', function (evt) {
                var mediaItem = $(this).parents('.cms-media-item');
                self.openIframeLayover(mediaItem.data('name'), $(this).attr('href'));
                evt.preventDefault();
            });

            $('.create-version', self.editContainer).on('click', function (evt) {
                var mediaItem = $(this).parents('.cms-media-item');
                self.openIframeLayover(CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.crop_window_title') + ' ' + mediaItem.data('name'), $(this).attr('href'));
                evt.preventDefault();
            });

            $('.edit-version', self.editContainer).on('click', function (evt) {
                var mediaItem = $(this).parents('.cms-media-item');
                self.openIframeLayover(CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.crop_window_title') + ' ' + mediaItem.data('name') + ' - ' + $(this).parent().find('.version-name').text(), $(this).attr('href'));
                evt.preventDefault();
            });
            $('.delete-version', self.editContainer).on('click', function (evt) {
                if (true === confirm(CHAMELEON.CORE.i18n.Translate('chameleon_system_media_manager.delete.are_you_sure'))) {
                    var mediaItem = $(this).parents('.cms-media-item');
                    var url = $(this).attr('href');
                    $.ajax({
                        type: "POST",
                        async: true,
                        url: url,
                        error: function (responseData) {
                            self.showErrorFromAjaxResponse();
                        },
                        success: function (jsonData) {
                            self.loadDetailPage(mediaItem.data('id'), mediaItem.data('name'));
                        },
                        dataType: 'JSON'
                    });
                }
                evt.preventDefault();
            });
            $('.delete-item', self.editContainer).on('click', function (evt) {
                self.confirmDeleteForMediaItemIds([$(this).parents('.cms-media-item').data('id')]);
                evt.preventDefault();
            });
            $('.pick-image', self.editContainer).on('click', function (evt) {
                const urlParams = new URLSearchParams(window.location.search);
                const parentField = urlParams.get('parentField');
                if (parentField) {
                    const iFrameName = parentField + '_iframe';
                    var siblingIframe = parent.document.getElementById(iFrameName);
                }

                var mediaItemContainer = $(this).parents('.cms-media-item');

                $.ajax({
                    type: "GET",
                    async: true,
                    url: self.settings.urls.postSelectUrl,
                    data: {
                        'mediaItemId': mediaItemContainer.data('id')
                    },
                    error: function (responseData) {
                        self.showErrorFromAjaxResponse();
                    },
                    success: function (jsonData) {
                        if (siblingIframe && siblingIframe.contentWindow) {
                            eval('siblingIframe.contentWindow.' + self.sanitizeCallbackFunctionName(self.state.pickImageCallback) + '("' + mediaItemContainer.data('id') + '")');
                        } else {
                            eval(self.sanitizeCallbackFunctionName(self.state.pickImageCallback) + '("' + mediaItemContainer.data('id') + '")');
                        }
                    },
                    dataType: 'JSON'
                });

                evt.preventDefault();
            });
            $('.pick-version', self.editContainer).on('click', function (evt) {
                var mediaItemContainer = $(this).parents('.cms-media-item');
                var cropId = $(this).data('crop-id');
                $.ajax({
                    type: "GET",
                    async: true,
                    url: self.settings.urls.postSelectUrl,
                    data: {
                        'mediaItemId': mediaItemContainer.data('id')
                    },
                    error: function (responseData) {
                        self.showErrorFromAjaxResponse();
                    },
                    success: function (jsonData) {
                        eval(self.sanitizeCallbackFunctionName(self.state.pickImageCallback) + '("' + mediaItemContainer.data('id') + '", "' + cropId + '")');
                    },
                    dataType: 'JSON'
                });

                evt.preventDefault();
            });
            $('.load-usages', self.editContainer).on('click', function(evt){
                CHAMELEON.CORE.showProcessingModal();
                var mediaItemContainer = $(this).parents('.cms-media-item');

                $.ajax({
                    type: "GET",
                    async: true,
                    url: self.settings.urls.mediaItemFindUsagesUrl,
                    data: {
                        'mediaItemId': mediaItemContainer.data('id')
                    },
                    error: function (responseData) {
                        self.showErrorFromAjaxResponse();
                    },
                    success: function (jsonData) {
                        CHAMELEON.CORE.hideProcessingModal();
                        $('#usages-list', self.editContainer).html(jsonData.contentHtml);
                    },
                    dataType: 'JSON'
                });

                evt.preventDefault();
            });
            $('.load-crops', self.editContainer).on('click', function(evt){
                CHAMELEON.CORE.showProcessingModal();
                var mediaItemContainer = $(this).parents('.cms-media-item');

                $.ajax({
                    type: "GET",
                    async: true,
                    url: self.settings.urls.mediaItemFindCropsUrl,
                    data: {
                        'mediaItemId': mediaItemContainer.data('id')
                    },
                    error: function (responseData) {
                        self.showErrorFromAjaxResponse();
                    },
                    success: function (jsonData) {
                        CHAMELEON.CORE.hideProcessingModal();
                        $('#crops-list', self.editContainer).html(jsonData.contentHtml);
                    },
                    dataType: 'JSON'
                });

                evt.preventDefault();
            });
        },
        open: function () {
            this.updateListView({});
        }
    });


    $.fn[pluginName] = function (state, options) {
        if (typeof state === 'string') {
            //call a method inside plugin
            return $.data(this[0], "plugin_" + pluginName)[state](options);
        }

        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, state, options));
            } else {
                $.data(this, "plugin_" + pluginName).open();
            }
        });
    };

})(jQuery, window, document);

(function ($) {

    "use strict";

    $('.snippetMediaManagerBackendModuleStandard').each(function () {
        var configurationObject = $(this).data('configuration');
        var stateObject = $(this).data('state');
        $(this).chameleonSystemMediaManager(stateObject, configurationObject);
    });

    $(".entry-id-copy-button").on("click", function() {
        CHAMELEON.CORE.copyToClipboard($(this).data("entry-id"));
    });

    //workaround for callback of universal uploader
    window.queueCompleteCallback = function () {
        $('.snippetMediaManagerBackendModuleStandard').chameleonSystemMediaManager('uploadCompleteCallback');
    };

    //workaround for old table list
    window.loadDetailPage = function ($id) {
        $('.snippetMediaManagerBackendModuleStandard').chameleonSystemMediaManager('loadDetailPage', $id);
    };

})(jQuery, window);
