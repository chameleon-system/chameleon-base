function restoreFieldValueVersion(id) {
    const valueColumnElement = document.querySelector(`table tr[data-record-id='${ id }'] *[data-field-restorable-value]`)

    if (!valueColumnElement) {
        return
    }

    const encodedRestorableValue = valueColumnElement.dataset["fieldRestorableValue"]

    try {
        const restorableValue = JSON.parse(encodedRestorableValue).value
        parent.postMessage(JSON.stringify({
            type: "restoreFieldValueVersion",
            valueId: id,
            valueContents: restorableValue
        }), window.parent.origin)
    } catch {
        console.error(`Failed to retrieve restorable value from attribute, can not post message.`)
    }
}