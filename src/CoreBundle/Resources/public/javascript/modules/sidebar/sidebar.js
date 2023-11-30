;(function ($, window, document, undefined) {
    "use strict";

    const pluginName = "chameleonSystemSidebarMenu";

    // NOTE this works parallel to bootstrap "dropdown.js" which reacts on the same css style classes with
    //   opening and closing items with "nav-dropdown".

    function Plugin(baseElement) {
        // debugger;
        this.$baseElement = $(baseElement);
        this.$navElement = this.$baseElement.find(".sidebar-nav");
        this.$navItems = this.$baseElement.find(".nav-item");
        this.$navTitles = this.$baseElement.find('.nav-group');
        this.$filterElement = this.$baseElement.find('.sidebar-filter-input');
        this.lastSearchTerm = '';
        this.scrollTopBeforeFilter = 0;

        // this.init();
    }
    $.extend(Plugin.prototype, {
        init: function () {
            // debugger;
            this.$filterElement.on('keyup', this.filter.bind(this));
            this.$baseElement.find('.nav-group-toggle').on('click', this.onCategoryToggle.bind(this));
            this.$navItems.on("click", this.onElementClick.bind(this));

            this.restoreOpenState();
            this.markSelected();

            $.extend($.expr[':'], {
                'chameleonContainsCaseInsensitive': function(elem, i, match, array) {
                    return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
                }
            });

            this.$baseElement.on("keyup", this.handleKeyEvent.bind(this));

            this.$filterElement.focus();

            this.handleScrollPosition();
        },
        restoreOpenState: function() {
            const activeCategoryIdsString = this.$baseElement.data('active-categories');
            const activeCategoryIds = activeCategoryIdsString.split(",");

            for (var i = 0; i < activeCategoryIds.length; i++) {
                this.$baseElement.find('[data-categoryid="' + activeCategoryIds[i] +'"]').addClass('open');
            }
        },
        markSelected: function() {
            var currentTableId = this.extractTableId(document.location.href);
            var documentPathAndSearch = document.location.pathname + document.location.search;

            var outer = this;

            this.$navItems.each(function() {
                var link = $(this).find("a").attr("href");
                var linkPathAndSearch = outer.getPathAndSearch(link);

                if (true === outer.entryUrlMatches(linkPathAndSearch, documentPathAndSearch)) {
                    $(this).addClass("selected-entry");

                    return;
                }

                var linkTableId = outer.extractTableId(link);

                if (linkTableId === currentTableId && linkTableId !== null) {
                    $(this).addClass("selected-entry");
                }
            });
        },
        handleScrollPosition: function() {
            this.$navElement.on("scroll", function(evt) {
                localStorage.setItem('sidebar-scroll-position', $(this).scrollTop());
            });

            if (null !== localStorage.getItem('sidebar-scroll-position')) {
                // animate (with default 400msec): needs to wait for "transition" of the css class "open" to finish (?)
                $(".sidebar-nav").animate({ scrollTop: localStorage.getItem('sidebar-scroll-position') }, 400);
            }
        },
        filter: function (event) {
            const searchTerm = this.$filterElement.val();
            if ('' !== this.lastSearchTerm && '' === searchTerm) {
                // display all again

                this.$navTitles.removeClass('d-none open');
                this.$navItems.removeClass('d-none');

                this.restoreOpenState();

                if (this.scrollTopBeforeFilter > 0) {
                    this.$navElement.scrollTop(this.scrollTopBeforeFilter);
                }
            }

            this.lastSearchTerm = searchTerm;

            if ('' === searchTerm) {
                return;
            }

            this.$navTitles.addClass('d-none').removeClass('open');
            this.$navItems.addClass('d-none');

            let $matchingNavItems = this.$navItems.find(":chameleonContainsCaseInsensitive('" + searchTerm + "')").closest('.nav-item');
            $matchingNavItems.removeClass('d-none');
            $matchingNavItems.parents('.nav-item').addClass('open').removeClass('d-none');

            let currentScrollTop = this.$navElement.scrollTop();
            if (this.$navElement.innerHeight() <= this.$baseElement.innerHeight() && currentScrollTop > 0) {
                // There are now fewer visible items than the scroll position shows

                this.scrollTopBeforeFilter = currentScrollTop;
                this.$navElement.scrollTop(0);
            }
        },
        handleKeyEvent: function(evt) {
            if ("ArrowDown" !== evt.key && "ArrowUp" !== evt.key && "Enter" !== evt.key) {
                return;
            }

            const $activeElement = $(document.activeElement);
            if (!$activeElement.hasClass("nav-item") && !$activeElement.hasClass("sidebar-filter-input")) {
                return;
            }

            // Special case filter input field
            if ($activeElement.is(this.$filterElement) && "ArrowDown" === evt.key) {
                const visibleItems = this.getVisibleNavItems();

                if (visibleItems.length > 0) {
                    visibleItems[0].focus();
                }

                return;
            }

            // Toggle category or activate link?
            if ("Enter" === evt.key) {
                if ($activeElement.hasClass("nav-dropdown")) {
                    this.toggleCategory($activeElement, true);
                } else {
                    const linkElement = $activeElement.find(".nav-link");
                    if (linkElement.length > 0) {
                        linkElement[0].click();
                    }
                }

                return;
            }

            // Normal arrow navigation
            if ($activeElement.is(".nav-item") && ("ArrowDown" === evt.key || "ArrowUp" === evt.key)) {
                let desiredNavItem = null;

                if ("ArrowDown" === evt.key) {
                    desiredNavItem = this.getNextVisibleNavItem($activeElement);
                } else {
                    // Can also be the text field (a non-nav item)

                    desiredNavItem = this.getPreviousVisibleNavItem($activeElement);
                }

                if (null !== desiredNavItem) {
                    $(desiredNavItem).focus();
                }
            }
        },
        getNextVisibleNavItem: function(activeElement) {
            const visibleItems = this.getVisibleNavItems();

            const idx = $(visibleItems).index(activeElement);

            if (idx > -1) {
                if (idx < visibleItems.length - 1) {
                    return visibleItems[idx + 1];
                } else {
                    return null;
                }
            }

            return null;
        },
        getPreviousVisibleNavItem: function(activeElement) {
            const visibleItems = this.getVisibleNavItems();

            const idx = $(visibleItems).index(activeElement);

            if (idx > -1) {
                if (idx > 0) {
                    return visibleItems[idx - 1];
                } else {
                    return this.$filterElement;
                }
            }

            return null;
        },
        getVisibleNavItems: function() {
            // NOTE not sure if this has the right order in every browser (depth first)
            return this.$baseElement.find(".nav-item.nav-dropdown, .nav-item.nav-dropdown.open .nav-item").not(".d-none").toArray();
        },
        onCategoryToggle: function (event) {
            let category = $(event.target).parent('.nav-dropdown');
            this.toggleCategory(category, false);
        },
        toggleCategory: function($category, byKeyboard) {
            const categoryId = $category.data('categoryid');
            const categoryOpen = $category.hasClass("open");

            if (byKeyboard) {
                $category.toggleClass("open");
            }
            // Else dropdown.js will react on the click

            var openArray = this.$baseElement.data('active-categories').split(",");

            if (!categoryOpen) {
                $category.focus();

                openArray.push(categoryId);
            } else {
                openArray = openArray.filter(function(value, index, arr) { return value !== categoryId });
            }

            this.$baseElement.data('active-categories', openArray.join(","));

            const url = this.$baseElement.data('toggle-category-notification-url');
            $.post(url, {
                categoryId: categoryId
            });
        },
        onElementClick: function (event) {
            this.handleElementClickForPopular($(event.target));
        },
        handleElementClickForPopular: function($clickedItem) {
            const clickedMenuId = $clickedItem.data("entry-id");

            if (0 === clickedMenuId.length) {
                return;
            }

            const url = this.$baseElement.data('element-click-notification-url');
            $.post(url, {
                clickedMenuId: clickedMenuId
            });
        },
        extractTableId: function(url) {
            var idx = url.indexOf("?");

            if (-1 === idx) {
                return null;
            }

            var urlParams = new URLSearchParams(url.substring(idx));

            if (url.includes("pagedef=tablemanager")) {
                return urlParams.get("id");
            } else if (url.includes("pagedef=tableeditor") || url.includes("templateengine")) {
                return urlParams.get("tableid");
            }

            return null;
        },
        getPathAndSearch: function(url) {
            var idx = url.indexOf("/");

            if (-1 === idx) {
                return "/";
            }

            return url.substring(idx);
        },
        entryUrlMatches: function(sidebarLink, documentLink) {
            // This matches the sidebarLink being part of the documentLink and
            // also matches if they are exactly equal.

            return sidebarLink === documentLink.substring(0, sidebarLink.length);
        }
    });

    $.fn[pluginName] = function (state, options) {
        if (typeof state === 'string') {
            //call a method inside plugin
            return $.data(this[0], "plugin_" + pluginName)[state](options);
        }

        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, state, options));
            } else {
                $.data(this, "plugin_" + pluginName).open();
            }
        });
    };
})(jQuery, window, document);

$(window).on("load.coreui.sidebar.data-api", function() {
    // Load it after sidebar initialization is finished
    $('.sidebar').chameleonSystemSidebarMenu();
});
