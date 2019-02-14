window.versionHistoryOverlayEventReceiver = undefined

class VersionHistoryOverlay {

    // Init

    constructor({element}) {
        this.element = element
        this.editor = this.getEditorFromElement(element)
        this.attributes = element.dataset

        if (!this.editor) {
            throw new TypeError(`Could not locate an instance of CKEditor to hook into.`)
        }
    }

    init() {
        const injectedElement = this.injectElement(this.element)
        this.attachEvents(injectedElement);
        this.initMessageListener()
    }

    getEditorFromElement(element) {
        const textareaElement = element.querySelector("textarea")
        if (!textareaElement) {
            return undefined
        }

        return CKEDITOR.instances[textareaElement.id]
    }

    // Message Listening

    initMessageListener() {
        window.addEventListener("message", event => {
            if (window.versionHistoryOverlayEventReceiver !== this) {
                return
            }

            const message = this.getMessageFromEvent(event)

            if (!message || message.type !== "restoreFieldValueVersion") {
                return undefined
            }

            this.setFieldValue(message.valueContents)
        }, false)
    }

    getMessageFromEvent(event) {
        let message = undefined

        try {
            message = JSON.parse(event.data)
        } catch {
            return undefined
        }

        return message
    }

    // Set Up

    injectElement(element) {
        const parentElement = element.querySelector(".cke .cke_inner .cke_toolbox")
        if (!parentElement) {
            return
        }

        const firstLineElement = parentElement.querySelector(".cke_toolbar")
        if (!firstLineElement) {
            return
        }

        const injectedElement = VersionHistoryElementProvider.element({
            enabled: this.attributes.fieldHasVersionHistory,
            title: this.attributes.fieldVersionHistoryTitle,
            numberOfFieldVersions: this.attributes.fieldNumberOfFieldVersions
        })

        const elementReference = injectedElement.firstChild
        parentElement.insertBefore(injectedElement, firstLineElement.nextElementSibling)

        return elementReference
    }

    attachEvents(injectedElement) {
        const anchorElement = injectedElement.querySelector("a.cke_button")
        if (!anchorElement) {
            return
        }

        anchorElement.addEventListener("click", event => {
            event.stopImmediatePropagation()
            this.showOverlay()
            return false
        })
    }

    // Overlay

    showOverlay() {
        window.versionHistoryOverlayEventReceiver = this
        CreateModalIFrameDialogCloseButton(this.attributes.fieldVersionHistoryViewUrl, 0, 0)
    }

    // Value

    setFieldValue(contents) {
        CloseModalIFrameDialog()
        this.editor.setData(contents)
    }

}

class VersionHistoryOverlayLoader {

    constructor() {
        this.modules = []
    }

    init() {
        for (const element of this.elements) {
            let moduleIsInitialized = false
            const module = new VersionHistoryOverlay({element})
            this.modules.push(module)

            const observer = new MutationObserver((mutationsList, observer) => {
                if (!moduleIsInitialized) {
                    moduleIsInitialized = true
                    module.init()
                }
            })

            observer.observe(element, {attributes: false, childList: true, subtree: false})
        }
    }

    get elements() {
        return document.querySelectorAll("[data-field-version-history-managed]")
    }

}

class VersionHistoryElementProvider {

    static element(args) {
        const innerHTML = VersionHistoryElementProvider.elementInnerHTML(args)
        return document.createRange().createContextualFragment(innerHTML)
    }

    static elementInnerHTML({enabled, title, numberOfFieldVersions}) {
        return `<span class="cke_toolgroup">
            <a class="cke_button cke_button_off" href="#" title="${ title }" tabindex="-1" hidefocus="true" role="button" ${ !enabled ? "disabled" : "" }>
                <span class="cke_button_label cke_button__source_label">${ title } (${ numberOfFieldVersions })</span>
                <span class="cke_button_label"></span>
            </a>
        </span>`
    }

}

(() => {
    document.addEventListener("DOMContentLoaded", event => {
        const loader = new VersionHistoryOverlayLoader()
        loader.init()
    })
})()