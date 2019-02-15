function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

window.versionHistoryOverlayEventReceiver = undefined;

var VersionHistoryOverlay =
    /*#__PURE__*/
    function () {
        "use strict";

        // Init
        function VersionHistoryOverlay(_ref) {
            var element = _ref.element;

            _classCallCheck(this, VersionHistoryOverlay);

            this.isLoaded = false;
            this.element = element;
            this.editor = this.getEditorFromElement(element);
            this.attributes = element.dataset;

            if (!this.editor) {
                throw new TypeError("Could not locate an instance of CKEditor to hook into.");
            }
        }

        _createClass(VersionHistoryOverlay, [{
            key: "init",
            value: function init() {
                this.isLoaded = true;
                var injectedElement = this.injectElement(this.element);
                this.attachEvents(injectedElement);
                this.initMessageListener();
            }
        }, {
            key: "getEditorFromElement",
            value: function getEditorFromElement(element) {
                var textareaElement = element.querySelector("textarea");

                if (!textareaElement) {
                    return undefined;
                }

                return CKEDITOR.instances[textareaElement.id];
            } // Message Listening

        }, {
            key: "initMessageListener",
            value: function initMessageListener() {
                var _this = this;

                window.addEventListener("message", function (event) {
                    if (window.versionHistoryOverlayEventReceiver !== _this) {
                        return;
                    }

                    var message = _this.getMessageFromEvent(event);

                    if (!message || message.type !== "restoreFieldValueVersion") {
                        return undefined;
                    }

                    _this.setFieldValue(message.valueContents);
                }, false);
            }
        }, {
            key: "getMessageFromEvent",
            value: function getMessageFromEvent(event) {
                try {
                    return JSON.parse(event.data);
                } catch (_unused) {
                    return undefined;
                }
            } // Set Up

        }, {
            key: "injectElement",
            value: function injectElement(element) {
                var parentElement = element.querySelector(".cke .cke_inner .cke_toolbox");

                if (!parentElement) {
                    return;
                }

                var firstLineElement = parentElement.querySelector(".cke_toolbar");

                if (!firstLineElement) {
                    return;
                }

                var injectedElement = VersionHistoryElementProvider.element({
                    enabled: this.attributes.fieldHasVersionHistory,
                    title: this.attributes.fieldVersionHistoryTitle,
                    numberOfFieldVersions: this.attributes.fieldNumberOfFieldVersions
                });
                var elementReference = injectedElement.firstChild;
                parentElement.insertBefore(injectedElement, firstLineElement.nextElementSibling);
                return elementReference;
            }
        }, {
            key: "attachEvents",
            value: function attachEvents(injectedElement) {
                var _this2 = this;

                var anchorElement = injectedElement.querySelector("a.cke_button");

                if (!anchorElement) {
                    return;
                }

                anchorElement.addEventListener("click", function (event) {
                    event.stopImmediatePropagation();

                    _this2.showOverlay();

                    return false;
                });
            } // Overlay

        }, {
            key: "showOverlay",
            value: function showOverlay() {
                window.versionHistoryOverlayEventReceiver = this;
                CreateModalIFrameDialogCloseButton(this.attributes.fieldVersionHistoryViewUrl, 0, 0);
            } // Value

        }, {
            key: "setFieldValue",
            value: function setFieldValue(contents) {
                CloseModalIFrameDialog();
                this.editor.setData(contents);
            }
        }]);

        return VersionHistoryOverlay;
    }();

var VersionHistoryOverlayLoader =
    /*#__PURE__*/
    function () {
        "use strict";

        function VersionHistoryOverlayLoader() {
            _classCallCheck(this, VersionHistoryOverlayLoader);

            this.modules = [];
        }

        _createClass(VersionHistoryOverlayLoader, [{
            key: "init",
            value: function init() {
                var _this3 = this;

                var _iteratorNormalCompletion = true;
                var _didIteratorError = false;
                var _iteratorError = undefined;

                try {
                    var _loop = function _loop() {
                        var element = _step.value;
                        var module = new VersionHistoryOverlay({
                            element: element
                        });

                        _this3.modules.push(module);

                        var observer = new MutationObserver(function (mutationsList, observer) {
                            if (!module.isLoaded) {
                                module.init();
                            }
                        });
                        observer.observe(element, {
                            attributes: false,
                            childList: true,
                            subtree: false
                        });
                    };

                    for (var _iterator = this.elements[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                        _loop();
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally {
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally {
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
            }
        }, {
            key: "elements",
            get: function get() {
                return document.querySelectorAll("[data-field-version-history-managed]");
            }
        }]);

        return VersionHistoryOverlayLoader;
    }();

var VersionHistoryElementProvider =
    /*#__PURE__*/
    function () {
        "use strict";

        function VersionHistoryElementProvider() {
            _classCallCheck(this, VersionHistoryElementProvider);
        }

        _createClass(VersionHistoryElementProvider, null, [{
            key: "element",
            value: function element(args) {
                var innerHTML = VersionHistoryElementProvider.elementInnerHTML(args);
                return document.createRange().createContextualFragment(innerHTML);
            }
        }, {
            key: "elementInnerHTML",
            value: function elementInnerHTML(_ref2) {
                var enabled = _ref2.enabled,
                    title = _ref2.title,
                    numberOfFieldVersions = _ref2.numberOfFieldVersions;
                return "<span class=\"cke_toolgroup\">\n            <a class=\"cke_button cke_button_off\" href=\"#\" title=\"".concat(title, "\" tabindex=\"-1\" hidefocus=\"true\" role=\"button\" ").concat(!enabled ? "disabled" : "", ">\n                <span class=\"cke_button_label cke_button__source_label\">").concat(title, " (").concat(numberOfFieldVersions, ")</span>\n                <span class=\"cke_button_label\"></span>\n            </a>\n        </span>");
            }
        }]);

        return VersionHistoryElementProvider;
    }();

(function () {
    document.addEventListener("DOMContentLoaded", function (event) {
        var loader = new VersionHistoryOverlayLoader();
        loader.init();
    });
})();