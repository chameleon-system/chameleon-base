window.TUIEditorManager = {
    editors: {},
    initEditor: function (editorId, config, inputFieldId, modalLink, dialogTitle) {
        const toastUiEditor = toastui.Editor;
        config.el = document.getElementById(editorId);
        const {tableMergedCell} = toastUiEditor.plugin;
        config.plugins = [tableMergedCell];
        const dialogElement = document.querySelector("#toastui-custom-button-dialog-" + editorId);
        
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
        this.addLinkButtonEvent(editorId, dialogElement);

        if (null !== dialogElement) {
            dialogElement.querySelector(".select-button").addEventListener("click", function (event) {
                event.preventDefault();
                let tableId = dialogElement.querySelector("select").selectedOptions[0].value;
                CreateModalIFrameDialog(modalLink + "&id=" + tableId, 0, 0, dialogTitle);
                $(dialogElement).modal("hide");
            });
        }

        this.editors[editorId].setMarkdown(inputElement.value);

        document.addEventListener("tableEditorBeforeSaveEvent", function (e) {
            inputElement.value = this.editors[editorId].getMarkdown();
        }.bind(this));
    },

    addLinkButtonEvent: function (editorId, dialogElement) {
        if (null === dialogElement) {
            return;
        }

        const editorElement = document.getElementById(editorId);
        const buttonElement = null !== editorElement ? editorElement.querySelector(".toastui-custom-button") : null;

        if (null !== buttonElement) {
            buttonElement.addEventListener("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                $(dialogElement).modal("show");
            });
        }
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
