function setInnerHtmlWithScripts(container, html) {
    container.innerHTML = html;

    const scripts = container.querySelectorAll("script");
    scripts.forEach(oldScript => {
        let newScript = document.createElement("script");

        // copy old script attributes to new script
        Array.from(oldScript.attributes).forEach(attr => {
            newScript.setAttribute(attr.name, attr.value);
        });

        // copy old script contents to new script
        if (oldScript.src) {
            newScript.src = oldScript.src;
        } else {
            newScript.textContent = oldScript.textContent;
        }

        oldScript.replaceWith(newScript);
    });
}

function prepareWidgetSpinner(widgetContainer) {
    // prep: spinner & fade-Out
    widgetContainer.style.transition = 'opacity 0.5s';
    widgetContainer.style.opacity = 0;
    widgetContainer.innerHTML = '<div class="loading-spinner">Lade Widget…</div>';
}

function updateWidget(data, widgetContainer, widgetSelector) {
    const parsedData = typeof data === "string" ? JSON.parse(data) : data;
    const { htmlTable, dateTime, title } = parsedData;
    setInnerHtmlWithScripts(widgetContainer, htmlTable);

    // fade-in after replace
    requestAnimationFrame(() => {
        widgetContainer.style.opacity = 1;
    });

    const titleElement = document.querySelector(`${widgetSelector} .widget-title`);
    if (titleElement) {
        titleElement.textContent = title;
    }

    const footerElement = document.querySelector(`${widgetSelector} .card-footer .widget-timestamp`);
    if (footerElement) {
        footerElement.textContent = dateTime;
    }
}

function dispatchWidgetLoadedEvent(serviceAlias, widgetContainer) {
    // dispatch a custom event after the widget is loaded
    const widgetLoadedEvent = new CustomEvent('widget:loaded', {
        detail: {
            widgetId: serviceAlias,
            widgetElement: widgetContainer
        }
    });
    document.dispatchEvent(widgetLoadedEvent);
}

function loadWidgetContent(serviceAlias, forceReload = false) {
    const widgetSelector = '#widget-' + serviceAlias.replace('widget-', '');
    const reloadUrl = `/cms/api/dashboard/widget/${serviceAlias}/getWidgetHtmlAsJson${forceReload ? '?forceReload=true' : ''}`;
    const widgetContainer = document.querySelector(`${widgetSelector} .lazy-widget`);

    if (!widgetContainer) return;

    prepareWidgetSpinner(widgetContainer)

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
        updateWidget(data, widgetContainer, widgetSelector);
    })
    .catch(error => {
        console.error("Error loading the widget data:", error);
    })
    .finally( () => {
        dispatchWidgetLoadedEvent(serviceAlias, widgetContainer)
    });
}

function initializeWidgetReload(buttonSelector) {
    const button = document.querySelector(buttonSelector);

    if (button) {
        button.addEventListener("click", function (event) {
            event.preventDefault();

            const serviceAlias = this.getAttribute("data-service-alias");
            loadWidgetContent(serviceAlias);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const sortableList = document.getElementById('sortable-list');
    const toggleEditModeButton = document.getElementById('toggle-edit-mode');
    const addWidgetButton = document.getElementById('add-widget-button');
    const widgetCollectionDropdown = document.getElementById('add-widget-collection');
    const widgetCollectionDropdownContainer = document.getElementById('add-widget-collection-container');


    let isEditMode = false;
    let draggedItem = null;

    // initial async load of all widgets
    document.querySelectorAll('.lazy-widget').forEach((widgetContainer) => {
        const serviceAlias = widgetContainer.getAttribute('data-service-alias');
        loadWidgetContent(serviceAlias);
    });

    // reload all widgets button
    const reloadAllButton = document.getElementById('reload-all-widgets');
    if (reloadAllButton) {
        reloadAllButton.addEventListener('click', (event) => {
            event.preventDefault();

            document.querySelectorAll('.lazy-widget').forEach((widgetContainer) => {
                const serviceAlias = widgetContainer.getAttribute('data-service-alias');
                loadWidgetContent(serviceAlias, true);
            });
        });
    }

    // Switch edit mode
    toggleEditModeButton.addEventListener('click', () => {
        isEditMode = !isEditMode;
        sortableList.classList.toggle('edit-mode', isEditMode);

        // Get button text and class attributes
        const buttonText = toggleEditModeButton.querySelector('span');
        const enableText = toggleEditModeButton.getAttribute('data-text-enable');
        const disableText = toggleEditModeButton.getAttribute('data-text-disable');
        const enableClass = toggleEditModeButton.getAttribute('data-class-enable');
        const disableClass = toggleEditModeButton.getAttribute('data-class-disable');

        if (buttonText) {
            buttonText.textContent = isEditMode ? disableText : enableText;
        }

        toggleEditModeButton.classList.remove(isEditMode ? disableClass : enableClass);
        toggleEditModeButton.classList.add(isEditMode ? enableClass : disableClass);

        if (isEditMode) {
            widgetCollectionDropdownContainer.classList.remove('d-none');
        } else {
            widgetCollectionDropdownContainer.classList.add('d-none');
        }

        Array.from(sortableList.querySelectorAll('.dashboard-widget-collection')).forEach((collection) => {
            collection.setAttribute('draggable', isEditMode ? 'true' : 'false');
        });

        Array.from(sortableList.querySelectorAll('.delete-icon')).forEach((icon) => {
            icon.style.display = isEditMode ? 'block' : 'none';
        });
    });

    // Drag-and-Drop handling
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

    // Delete a widget collection
    sortableList.addEventListener('click', (event) => {
        if (isEditMode && event.target.classList.contains('delete-collection')) {
            const collection = event.target.closest('.dashboard-widget-collection');
            if (collection) {
                collection.remove();
                saveWidgetLayout();
            }
        }
    });

    // Add new widget collection
    if (addWidgetButton) {
        addWidgetButton.addEventListener('click', () => {
            const selectedCollection = widgetCollectionDropdown.value;
            if (!selectedCollection) {
                return;
            }

            // Aktuelles Layout abrufen
            let layout = Array.from(sortableList.querySelectorAll('.dashboard-widget-collection'))
                .map((item) => item.dataset.id);

            if (layout.includes(selectedCollection)) {
                alert('Collection already in dashboard.');
                return;
            }

            layout.unshift(selectedCollection);

            saveWidgetLayout(layout, true);
        });
    }

    // Save widget layout
    const saveWidgetLayout = (layout = null, reload = false) => {
        const widgetLayout = layout || Array.from(sortableList.querySelectorAll('.dashboard-widget-collection'))
            .map((item) => item.dataset.id);

        fetch('/cms/api/dashboard/save-widget-layout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ widgetLayout })
        }).then((response) => {
            if (!response.ok) {
                console.error('Error saving the layout');
                return;
            }
            if (reload) {
                location.reload();
            }
        }).catch((error) => console.error('Fetch-Fehler:', error));
    };
});
