function initializeWidgetReload(buttonSelector) {
    const button = document.querySelector(buttonSelector);

    if (button) {
        button.addEventListener("click", function (event) {
            event.preventDefault();

            const serviceAlias = this.getAttribute("data-service-alias");
            const widgetSelector = '#widget-' + serviceAlias.replace('widget-', '');
            const reloadUrl = `/cms/api/dashboard/widget/${serviceAlias}/getWidgetHtmlAsJson`;

            fetch(reloadUrl, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json"
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP-Error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const parsedData = typeof data === "string" ? JSON.parse(data) : data;
                    const { htmlTable, dateTime } = parsedData;

                    const targetDiv = document.querySelector(`${widgetSelector} .card-body`);
                    if (targetDiv) {
                        targetDiv.style.opacity = 0;

                        setTimeout(() => {
                            targetDiv.innerHTML = htmlTable;
                            targetDiv.style.transition = "opacity 0.5s";
                            targetDiv.style.opacity = 1;
                        }, 300);
                    }

                    const footerElement = document.querySelector(`${widgetSelector} .card-footer .widget-timestamp`);
                    if (footerElement) {
                        footerElement.textContent = dateTime;
                    }
                })
                .catch(error => {
                    console.error("Error loading the widget data:", error);
                });
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const sortableList = document.getElementById('sortable-list');
    const toggleEditModeButton = document.getElementById('toggle-edit-mode');
    let isEditMode = false;
    let draggedItem = null;

    // switch edit mode
    toggleEditModeButton.addEventListener('click', () => {
        isEditMode = !isEditMode;
        sortableList.classList.toggle('edit-mode', isEditMode);

        // Get button text and class attributes
        const buttonText = toggleEditModeButton.querySelector('span');
        const enableText = toggleEditModeButton.getAttribute('data-text-enable');
        const disableText = toggleEditModeButton.getAttribute('data-text-disable');
        const enableClass = toggleEditModeButton.getAttribute('data-class-enable');
        const disableClass = toggleEditModeButton.getAttribute('data-class-disable');

        // Change the button text dynamically
        if (buttonText) {
            buttonText.textContent = isEditMode ? disableText : enableText;
        }

        // Change the button class dynamically
        toggleEditModeButton.classList.remove(isEditMode ? disableClass : enableClass);
        toggleEditModeButton.classList.add(isEditMode ? enableClass : disableClass);

        // Enable or disable drag-and-drop
        Array.from(sortableList.querySelectorAll('.dashboard-widget-collection')).forEach((collection) => {
            collection.setAttribute('draggable', isEditMode ? 'true' : 'false');
        });

        // Show or hide delete icons
        Array.from(sortableList.querySelectorAll('.delete-icon')).forEach((icon) => {
            icon.style.display = isEditMode ? 'block' : 'none';
        });
    });

    // Drag-and-Drop-Events initialisieren
    sortableList.addEventListener('dragstart', (event) => {
        if (isEditMode && event.target.classList.contains('dashboard-widget-collection')) {
            draggedItem = event.target;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', draggedItem.dataset.id);
            draggedItem.classList.add('dragging');
        }
    });

    sortableList.addEventListener('dragover', (event) => {
        if (isEditMode) {
            event.preventDefault();
            const target = event.target.closest('.dashboard-widget-collection');
            if (target && target !== draggedItem) {
                const bounding = target.getBoundingClientRect();
                const offset = bounding.y + bounding.height / 2;
                const parent = target.parentNode;

                // Insert before or after based on mouse position
                if (event.clientY - offset > 0) {
                    parent.insertBefore(draggedItem, target.nextSibling);
                } else {
                    parent.insertBefore(draggedItem, target);
                }
            }
        }
    });

    sortableList.addEventListener('drop', (event) => {
        if (isEditMode) {
            event.preventDefault();
            if (draggedItem) {
                draggedItem.classList.remove('dragging');
                saveWidgetLayout();
            }
        }
    });

    sortableList.addEventListener('dragend', () => {
        if (isEditMode && draggedItem) {
            draggedItem.classList.remove('dragging');
        }
    });

    // delete a widget collection
    sortableList.addEventListener('click', (event) => {
        if (isEditMode && event.target.classList.contains('delete-collection')) {
            const collection = event.target.closest('.dashboard-widget-collection');
            if (collection) {
                collection.remove(); // Remove the entire widget collection
                saveWidgetLayout(); // Save the remaining layout
            }
        }
    });

    const saveWidgetLayout = () => {
        const widgetLayout = Array.from(sortableList.querySelectorAll('.dashboard-widget-collection'))
            .map((item) => item.dataset.id);

        fetch('/cms/api/dashboard/save-widget-layout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ widgetLayout: widgetLayout })
        }).then((response) => {
            if (!response.ok) {
                console.error('Error saving the layout');
            }
        });
    };
});