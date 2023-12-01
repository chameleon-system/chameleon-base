;(function ($, window, document, undefined) {
    "use strict";

    const pluginName = "chameleonSystemSidebarMenu";

    // NOTE this works parallel to "coreui.js" which reacts on the same css style classes with
    // opening and closing items with "nav-group".

    function Plugin(baseElement) {
        this.$baseElement = $(baseElement);
        this.$navElement = this.$baseElement.find(".sidebar-nav");
        this.$navItems = this.$baseElement.find(".nav-item");
        this.$navTitles = this.$baseElement.find('.nav-group');
        this.$filterElement = this.$baseElement.find('.sidebar-filter-input');
        this.lastSearchTerm = '';
        this.scrollTopBeforeFilter = 0;

        this.init();
    }
    $.extend(Plugin.prototype, {
        init: function () {
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
            const activeCategoryId = this.$baseElement.data('active-category');
            this.$baseElement.find('[data-categoryid="' + activeCategoryId +'"]').addClass('show open').attr('aria-expanded', 'true');
        },
        markSelected: function() {
            var currentTableId = this.extractTableId(document.location.href);
            var documentPathAndSearch = document.location.pathname + document.location.search;

            var outer = this;

            this.$navItems.each(function() {
                var link = $(this).find("a").attr("href");
                var linkPathAndSearch = outer.getPathAndSearch(link);

                if (true === outer.entryUrlMatches(linkPathAndSearch, documentPathAndSearch)) {
                    $(this).find('.nav-link').addClass("active");
                    return;
                }

                var linkTableId = outer.extractTableId(link);

                if (linkTableId === currentTableId && linkTableId !== null) {
                    $(this).find('.nav-link').addClass("active");
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

                this.$navTitles.removeClass('d-none show');
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

            this.$navTitles.addClass('d-none').removeClass('show').attr('aria-expanded', 'false');
            this.$navItems.addClass('d-none');

            let $matchingNavItems = this.$navItems.find(":chameleonContainsCaseInsensitive('" + searchTerm + "')").closest('.nav-item');
            $matchingNavItems.removeClass('d-none');
            $matchingNavItems.parents('.nav-group').removeClass('d-none').addClass('show').attr('aria-expanded', 'true');

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

            let $activeElement = $(document.activeElement);
            if (!$activeElement.hasClass("nav-item") && !$activeElement.hasClass("nav-group") && !$activeElement.hasClass("sidebar-filter-input")) {
                if ($activeElement.hasClass('nav-link')) {
                    $activeElement = $activeElement.parent();
                } else {
                    return;
                }

            }

            // Special case filter input field
            if ($activeElement.is(this.$filterElement) && "ArrowDown" === evt.key) {
                const visibleItems = this.getVisibleNavItems();

                if (visibleItems.length > 0) {
                    const linkElement = visibleItems.eq(0).find("> .nav-link");
                    if (linkElement.length > 0) {
                        linkElement.eq(0).focus();
                    }
                }

                return;
            }

            // Toggle category or activate link?
            if ("Enter" === evt.key) {
                if ($activeElement.hasClass("nav-group")) {
                    debugger;
                    // @ToDo: this doesn't really work yet!!!!
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
            if (($activeElement.is(".nav-item") || ($activeElement.is(".nav-group"))) && ("ArrowDown" === evt.key || "ArrowUp" === evt.key)) {
                let desiredNavItem = null;

                if ("ArrowDown" === evt.key) {
                    desiredNavItem = this.getNextVisibleNavItem($activeElement);
                } else {
                    // Can also be the text field (a non-nav item)
                    desiredNavItem = this.getPreviousVisibleNavItem($activeElement);
                }

                if (null !== desiredNavItem) {
                    if (desiredNavItem.hasClass('sidebar-filter-input')) {
                        desiredNavItem.focus();
                        return;
                    }
                    let linkElement = desiredNavItem.find("> .nav-link");
                    if (linkElement.length > 0) {
                        linkElement.eq(0).focus();
                    }
                }
            }
        },
        getNextVisibleNavItem: function(activeElement) {
            const visibleItems = this.getVisibleNavItems();

            const idx = $(visibleItems).index(activeElement);

            if (idx > -1) {
                if (idx < visibleItems.length - 1) {
                    return visibleItems.eq(idx + 1);
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
                    return visibleItems.eq(idx - 1);
                } else {
                    return this.$filterElement;
                }
            }

            return null;
        },
        getVisibleNavItems: function() {
            // NOTE not sure if this has the right order in every browser (depth first)
            return this.$baseElement.find(".nav-item, .nav-group").not(".d-none");
        },
        onCategoryToggle: function (event) {
            let category = $(event.target).parent('.nav-group');
            this.toggleCategory(category, false);
        },
        toggleCategory: function($category, byKeyboard) {
            let categoryId = $category.data('categoryid');

            if ($category.hasClass("open")) {
                //active open category is closed and must be removed from the session
                $category.removeClass('open');
                categoryId = '';
            } else {
                this.$navTitles.removeClass('open');
                $category.addClass('open');
            }

            if (byKeyboard) {
                $category.toggleClass("show");
                $category.attr('aria-expanded', $category.hasClass("show") ? 'true' : 'false');
            }
            // Else dropdown.js will react on the click

            this.$baseElement.data('active-category', categoryId);

            const url = this.$baseElement.data('toggle-category-notification-url');
            $.post(url, {
                categoryId: categoryId
            });
        },
        onElementClick: function (event) {
            this.handleElementClickForPopular($(event.target));
        },
        handleElementClickForPopular: function($clickedItem) {
            // @ToDo: TEST THIS!!!
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
            // @ToDo: TEST THIS!!!
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
