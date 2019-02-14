function restoreFieldValueVersion(id) {
    var valueColumnElement = document.querySelector("table tr[data-record-id='".concat(id, "'] *[data-field-restorable-value]"));

    if (!valueColumnElement) {
        return;
    }

    var encodedRestorableValue = valueColumnElement.dataset["fieldRestorableValue"];

    try {
        var restorableValue = JSON.parse(encodedRestorableValue).value;
        parent.postMessage(JSON.stringify({
            type: "restoreFieldValueVersion",
            valueId: id,
            valueContents: restorableValue
        }), window.parent.origin);
    } catch (_unused) {
        console.error("Failed to retrieve restorable value from attribute, can not post message.");
    }
}