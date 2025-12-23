if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {};
}
CHAMELEON.CORE = CHAMELEON.CORE || {};
CHAMELEON.CORE.FieldIconFontSelector = CHAMELEON.CORE.FieldIconFontSelector || {};

CHAMELEON.CORE.FieldIconFontSelector =
{
    openDialog: function (fieldName, title) {
        const content = document.getElementById(fieldName+'-icon-list').innerHTML;
        CHAMELEON.CORE.showModal(title, content, 'modal-xxl');
        
        // Use event delegation for tab clicks because content is injected dynamically
        const modalElement = document.getElementById('modalDialog');
        if (modalElement && !modalElement.dataset.tabsInitialized) {
            modalElement.addEventListener('click', function (event) {
                const tabLink = event.target.closest('.icon-tabs a[data-coreui-toggle="tab"], .icon-tabs a[data-toggle="tab"]');
                if (!tabLink) return;

                event.preventDefault();
                
                // Deactivate current active tab
                const tabList = tabLink.closest('.icon-tabs');
                tabList.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                    link.setAttribute('aria-selected', 'false');
                });
                
                // Activate clicked tab
                tabLink.classList.add('active');
                tabLink.setAttribute('aria-selected', 'true');
                
                // Switch tab pane
                const targetId = tabLink.getAttribute('href').substring(1);
                const tabContent = modalElement.querySelector('.icon-tab-content');
                if (tabContent) {
                    tabContent.querySelectorAll('.tab-pane').forEach(pane => {
                        if (pane.id === targetId) {
                            pane.classList.add('show', 'active');
                            pane.style.display = 'block';
                        } else {
                            pane.classList.remove('show', 'active');
                            pane.style.display = 'none';
                        }
                    });
                }
            });
            modalElement.dataset.tabsInitialized = "true";
        }
    },

    selectIconClass: function (iconElement, fieldName) {
        const iconClass = iconElement.dataset.cssClass;
        document.getElementById(fieldName).value = iconClass;
        document.getElementById(fieldName+'-active-icon').className = iconClass;
        CloseModalIFrameDialog();
    },

    search: function (searchInput) {
        const searchText = searchInput.value.toLowerCase();
        const iconListContainer = searchInput.closest('.modal-body');
        const icons = iconListContainer.querySelectorAll('span[data-css-class]');
        const tabPanes = iconListContainer.querySelectorAll('.tab-pane');
        const iconTabs = iconListContainer.querySelector('.icon-tabs');
        const categoryHeaders = iconListContainer.querySelectorAll('.icon-category-header');
        const noResultsMessage = iconListContainer.querySelector('.no-results-message');
        let totalVisibleIcons = 0;

        if (searchText.length >= 2) {
            if (iconTabs) iconTabs.style.display = 'none';
            tabPanes.forEach(pane => {
                pane.classList.add('show', 'active');
                pane.classList.remove('fade');
                pane.style.display = 'block';
            });
        } else {
            if (iconTabs) iconTabs.style.display = '';
            tabPanes.forEach(pane => {
                pane.classList.add('fade');
                const tabId = pane.id;
                const tabLink = iconListContainer.querySelector('a[href="#' + tabId + '"]');
                if (tabLink && tabLink.classList.contains('active')) {
                    pane.classList.add('show', 'active');
                    pane.style.display = 'block';
                } else {
                    pane.classList.remove('show', 'active');
                    pane.style.display = 'none';
                }
            });
        }

        icons.forEach(icon => {
            const cssClass = icon.dataset.cssClass.toLowerCase();
            if (searchText.length < 2 || cssClass.includes(searchText)) {
                icon.style.setProperty('display', 'flex', 'important');
                totalVisibleIcons++;
            } else {
                icon.style.setProperty('display', 'none', 'important');
            }
        });

        // Hide categories if no icons are visible in them
        categoryHeaders.forEach(header => {
            const row = header.nextElementSibling;
            if (row && row.classList.contains('row')) {
                const iconsInRow = row.querySelectorAll('span[data-css-class]');
                let hasVisibleIcons = false;
                iconsInRow.forEach(icon => {
                    if (icon.style.display !== 'none') {
                        hasVisibleIcons = true;
                    }
                });
                
                const pane = header.closest('.tab-pane');
                if (hasVisibleIcons) {
                    header.style.display = '';
                    row.style.display = '';
                } else {
                    header.style.display = 'none';
                    row.style.display = 'none';
                }
            }
        });

        if (noResultsMessage) {
            noResultsMessage.style.display = totalVisibleIcons === 0 ? 'block' : 'none';
        }
    }
}
