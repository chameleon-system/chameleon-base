class VersionHistoryOverlay {

    // Init

    constructor({element}) {
        this.element = element
        this.attributes = element.dataset
    }

    init() {
        const injectedElement = this.inject(this.element)
        this.attach(injectedElement);
    }

    // Element Manipulation

    inject(element) {
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

    attach(injectedElement) {
        const anchorElement = injectedElement.querySelector("a.cke_button")
        if (!anchorElement) {
            return
        }

        anchorElement.addEventListener("click", event => {
            this.overlay()
        })
    }

    overlay() {
        CreateModalIFrameDialogCloseButton(this.attributes.fieldVersionHistoryViewUrl , 0, 0)
    }

}

class VersionHistoryOverlayLoader {

    init() {
        const modules = []

        for (const element of this.elements) {
            let moduleIsInitialized = false
            const module = new VersionHistoryOverlay({element})
            modules.push(module)

            const observer = new MutationObserver((mutationsList, observer) => {
                if (!moduleIsInitialized) {
                    moduleIsInitialized = true
                    module.init()
                }
            })
            observer.observe(element, {attributes: false, childList: true, subtree: false})
        }

        return modules
    }

    get elements() {
        return document.querySelectorAll("[data-field-version-history-managed]")
    }

}

class VersionHistoryElementProvider {

    static element(args) {
        const innerHTML = VersionHistoryElementProvider.elementInnerHTML(args)
        const fragment = document.createRange().createContextualFragment(innerHTML)

        return fragment
    }

    static elementInnerHTML({enabled, title, numberOfFieldVersions}) {
        return `<span class="cke_toolgroup" role="presentation">
            <a id="cke_11" class="cke_button cke_button__version_history cke_button_off" href="#" title="${ title }" tabindex="-1" hidefocus="true" role="button" aria-labelledby="cke_11_label" aria-describedby="cke_11_description" aria-haspopup="false" ${ !enabled ? "disabled" : "" }>
                <!--<span class="cke_button_icon cke_button__source_icon" style="background-image: url('/chameleon/blackbox/components/versionHistory/restore.svg');">&nbsp;</span>-->
                <span id="cke_11_label" class="cke_button_label cke_button__source_label" aria-hidden="false">${ title } (${ numberOfFieldVersions })</span>
                <span id="cke_11_description" class="cke_button_label" aria-hidden="false"></span>
            </a>
        </span>`
    }

}

(() => {
    document.addEventListener("DOMContentLoaded", event => {
        const loader = new VersionHistoryOverlayLoader()
        const modules = loader.init()
    })
})()