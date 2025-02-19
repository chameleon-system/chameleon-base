if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {};
}
CHAMELEON.CORE = CHAMELEON.CORE || {};

CHAMELEON.CORE.SortableList = (function () {
    let posList, movedItemInput, draggedItem, sortFieldName;

    function init(fieldName) {
        posList = document.getElementById("posList");
        movedItemInput = document.getElementById("movedItemID");
        sortFieldName = fieldName;

        if (!posList) {
            console.warn("SortableList: #posList not found.");
            return;
        }

        posList.querySelectorAll("li:not(.disabled)").forEach((item) => {
            item.draggable = true;
            item.addEventListener("dragstart", handleDragStart);
            item.addEventListener("dragover", handleDragOver);
            item.addEventListener("drop", handleDrop);
            item.addEventListener("dragend", handleDragEnd);
        });
    }

    function handleDragStart(event) {
        draggedItem = this;
        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.setData("text/plain", this.id);
        this.classList.add("dragging");
    }

    function handleDragOver(event) {
        event.preventDefault();
        const afterElement = getDragAfterElement(posList, event.clientY);
        if (afterElement == null) {
            posList.appendChild(draggedItem);
        } else {
            posList.insertBefore(draggedItem, afterElement);
        }
    }

    function handleDrop(event) {
        event.preventDefault();
    }

    function handleDragEnd() {
        this.classList.remove("dragging");
        updateOrder();
    }

    function getDragAfterElement(container, y) {
        const elements = [...container.querySelectorAll("li:not(.dragging)")];
        return elements.reduce(
            (closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                return offset < 0 && offset > closest.offset ? { offset, element: child } : closest;
            },
            { offset: Number.NEGATIVE_INFINITY }
        ).element;
    }

    function updateOrder() {
        const items = [...posList.children].filter(item => !item.classList.contains("disabled"));

        items.forEach((item) => {
            const hiddenInput = item.querySelector("input[name='aPosOrder[]']");
            if (hiddenInput) {
                hiddenInput.value = item.id.replace("item", "");
            }
        });

        // set the id of the moved element
        movedItemInput.value = draggedItem ? draggedItem.id.replace("item", "") : "";

        // set the new order in the parent form
        if (parent.document.cmseditform && sortFieldName in parent.document.cmseditform) {
            parent.document.cmseditform[sortFieldName].value = movedItemInput.value;
        }

        PostAjaxForm("poslistform", sortAjaxCallback);
    }

    function sortAjaxCallback(data) {
        if (parent.document.cmseditform && sortFieldName in parent.document.cmseditform) {
            parent.document.cmseditform[sortFieldName].value = data;
        }
        CloseModalIFrameDialog();
    }

    return {
        init: init
    };
})();
