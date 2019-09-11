;(function ($, window, document, undefined) {
    "use strict";

    const pluginName = "chameleonSystemSidebarMenu";

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
            const self = this;

            this.$filterElement.on('keyup', this.filter.bind(this));
            this.$baseElement.find('.sidebar-minimizer').on('click', this.onSidebarToggle.bind(this));
            this.$baseElement.find('.nav-dropdown-toggle').on('click', this.onCategoryToggle.bind(this));

            this.$baseElement.find('[data-categoryid="' + this.$baseElement.data('active-category') + '"]').addClass('open');

            $.extend($.expr[':'], {
                'chameleonContainsCaseInsensitive': function(elem, i, match, array) {
                    return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
                }
            });

            // TODO this can be more specific (see keyup above)
            $(document).on("keyup", self.handleKeyEvent.bind(this));

            this.$filterElement.focus();
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
                console.log(this.$navItems.first());
                this.$navItems.first().focus();

                return;
            }

            // Toggle category or activate link?
            if ("Enter" === evt.key) {
                if ($activeElement.hasClass("nav-dropdown")) {
                    this.toggleCategory($activeElement);
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

                // TODO cache "visible items" somehow - invalidate when expanding
                if ("ArrowDown" === evt.key) {
                    desiredNavItem = this.getNextVisibleNavItem($activeElement);
                } else {
                    desiredNavItem = this.getPreviousVisibleNavItem($activeElement);
                }

                if (null !== desiredNavItem) {
                    $(desiredNavItem).focus();
                }
            }
        },
        getNextVisibleNavItem: function(activeElement) {
            const visibleItems = this.getVisibleNavItems();

            for (let i=0; i<visibleItems.length; i++) {
                if ($(visibleItems[i]).is(activeElement)) {
                    if (i < visibleItems.length - 1) {
                        return visibleItems[i + 1];
                    } else {
                        return null;
                    }
                }
            }

            return null;
        },
        getPreviousVisibleNavItem: function(activeElement) {
            const visibleItems = this.getVisibleNavItems();

            for (let i=0; i<visibleItems.length; i++) {
                if ($(visibleItems[i]).is(activeElement)) {
                    if (i > 0) {
                        return visibleItems[i - 1];
                    } else {
                        return this.$filterElement;
                    }
                }
            }

            return null;
        },
        getVisibleNavItems: function() {
            let nonFilteredNavItems = this.$navTitles.not(".d-none");

            // TODO with filtering there is more than one opened item
            let openedNavItem = nonFilteredNavItems.filter(".open");
            if (openedNavItem.length === 0) {
                return nonFilteredNavItems;
            }

            let visibleNavItems = [];

            nonFilteredNavItems.each(function () {
                visibleNavItems.push(this);

                if ($(this).is(openedNavItem)) {
                    openedNavItem.find(".nav-item").not(".d-none").each(function() {
                        visibleNavItems.push(this);
                    });
                }
            });

            return visibleNavItems;
        },
        filter: function (event) {
            const searchTerm = event.target.value;
            if ('' !== this.lastSearchTerm && '' === searchTerm) {
                // display all again

                this.$navTitles.removeClass('d-none open');
                this.$navItems.removeClass('d-none');
            }

            this.lastSearchTerm = searchTerm;

            if ('' === searchTerm) {
                return;
            }

            if ('undefined' === typeof this.$navItems) {
                console.log("Strange undefined case");

                this.$navTitles = this.$baseElement.find('.nav-dropdown');
                this.$navItems = this.$baseElement.find('.nav-dropdown-items .nav-item');
                // TODO these are/were only the leaf items
            }

            this.$navTitles.addClass('d-none').removeClass('open');
            this.$navItems.addClass('d-none');

            const $matchingNavItems = this.$navItems.find(":chameleonContainsCaseInsensitive('" + searchTerm + "')").closest('.nav-item');
            $matchingNavItems.removeClass('d-none');
            $matchingNavItems.parents('.nav-item').addClass('open').removeClass('d-none');
        },
        onCategoryToggle: function (event) {
            const $category = $(event.target).parent('.nav-dropdown');

            this.toggleCategory($category);
        },
        toggleCategory: function($category) {
            const categoryOpen = $category.hasClass("open");
            let categoryId = null;

            if (categoryOpen) {
                $category.removeClass("open");

                // TODO this is correct in filtered state?
            } else {
                // Else close others and open this one
                this.$baseElement.find('.nav-dropdown.open').not($category).removeClass('open');
                $category.addClass("open");

                categoryId = $category.data('categoryid');
            }

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
