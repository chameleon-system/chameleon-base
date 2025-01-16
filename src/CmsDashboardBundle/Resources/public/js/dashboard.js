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
