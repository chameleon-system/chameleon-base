;(function ($, window, document, undefined) {
    "use strict";

    const pluginName = "chameleonSystemSidebarMenu";

    // NOTE this works parallel to bootstrap "dropdown.js" which reacts on the same css style classes with
    //   opening and closing items with "nav-dropdown".

    function Plugin(baseElement) {
        this.$baseElement = $(baseElement);
        this.$navItems = this.$baseElement.find(".nav-item");
        this.$navTitles = this.$baseElement.find('.nav-dropdown');
        this.$filterElement = this.$baseElement.find('.sidebar-filter-input');
        this.lastSearchTerm = '';

        this.init();
    }
    $.extend(Plugin.prototype, {
        init: function () {
            this.$filterElement.on('keyup', this.filter.bind(this));
            this.$baseElement.find('.sidebar-minimizer').on('click', this.onSidebarToggle.bind(this));
            this.$baseElement.find('.nav-dropdown-toggle').on('click', this.onCategoryToggle.bind(this));

            this.$baseElement.find('[data-categoryid="' + this.$baseElement.data('active-category') + '"]').addClass('open');

            $.extend($.expr[':'], {
                'chameleonContainsCaseInsensitive': function(elem, i, match, array) {
                    return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
                }
            });

            this.$baseElement.on("keyup", this.handleKeyEvent.bind(this));

            this.$filterElement.focus();
        },
        filter: function (event) {
            const searchTerm = this.$filterElement.val();
            if ('' !== this.lastSearchTerm && '' === searchTerm) {
                // display all again

                this.$navTitles.removeClass('d-none open');
                this.$navItems.removeClass('d-none');
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
                    this.toggleCategory($activeElement, false);
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
                    // TODO quite odd to get a non-nav item (text field) here when being on top of the list

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
            this.toggleCategory(category, true);
        },
        toggleCategory: function($category, clicked) {
            // TODO the parameter "clicked" is quite not elegant

            const categoryOpen = $category.hasClass("open");
            let categoryId = null;

            if (!categoryOpen) {
                if ('' === this.$filterElement.val()) {
                    // Without filter close others before opening this one
                    this.$baseElement.find('.nav-dropdown.open').not($category).removeClass('open');
                }

                categoryId = $category.data('categoryid');
            }

            if (!clicked) {
                $category.toggleClass("open");
            }
            // Else dropdown.js will react on the click

            $category.focus();

            const url = this.$baseElement.data('save-active-category-notification-url');
            $.post(url, {
                categoryId: categoryId
            });
        },
        onSidebarToggle: function () {
            const url = this.$baseElement.data('toggle-notification-url');
            let displayState;
            // The following condition is inverted, as this handler will be executed before the actual class change.
            if (document.body.classList.contains('sidebar-minimized')) {
                displayState = 'shown';
            } else {
                displayState = 'minimized';
            }
            $.post(url, {
                displayState: displayState
            });
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

(function ($) {
    $('.sidebar').chameleonSystemSidebarMenu();
})(jQuery);
