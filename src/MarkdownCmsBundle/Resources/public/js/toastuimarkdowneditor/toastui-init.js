window.TUIEditorManager = {
    editors: {},
    initEditor: function (editorId, config, inputFieldId, modalLink, dialogTitle) {
        const toastUiEditor = toastui.Editor;
        config.el = document.getElementById(editorId);
        const {tableMergedCell} = toastUiEditor.plugin;
        config.plugins = [tableMergedCell];
        
        // this fixes, somehow the problem, that the preview mode renders hard breaks for soft brakes
        config.customHTMLRenderer = {
            softbreak(_, {options}) {
                return {
                    type: 'html',
                    content: "\n\n"
                };
            }
        };

        this.editors[editorId] = new toastUiEditor.factory(config);

        const inputElement = document.getElementById(inputFieldId);

        this.addLinkButton(editorId);
        this.addLinkButtonEvent(editorId);

        document.querySelector("#toastui-custom-button-dialog-" + editorId + " .select-button").addEventListener("click", function (event) {
            event.preventDefault();
            let tableId = document.querySelector(".toastui-modal .modal-dialog select").selectedOptions[0].value;
            CreateModalIFrameDialog(modalLink + "&id=" + tableId, 0, 0, dialogTitle);
            $("#toastui-custom-button-dialog-" + editorId).modal("hide");
        });

        this.editors[editorId].setMarkdown(inputElement.value);

        document.addEventListener("tableEditorBeforeSaveEvent", function (e) {
            inputElement.value = this.editors[editorId].getMarkdown();
        }.bind(this));
    },

    addLinkButtonEvent: function (editorId) {
        document.addEventListener("click", function (event) {
            if (event.target.matches("#" + editorId + " .toastui-custom-button")) {
                $("#toastui-custom-button-dialog-" + editorId).modal();
            }
        });
    },

    addLinkButton: function (editorId) {
        this.editors[editorId].insertToolbarItem(
            {
                groupIndex: 3,
                itemIndex: 2
            }, {
                name: "cmslink",
                tooltip: "CMS Link",
                text: "\uf15b",
                className: "fa toastui-custom-button toastui-editor-toolbar-icons first",
                style: {
                    backgroundImage: "none"
                }
            });
    }
}
