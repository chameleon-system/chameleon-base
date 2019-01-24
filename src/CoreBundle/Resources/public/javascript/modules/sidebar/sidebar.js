;(function ($, window, document, undefined) {
    "use strict";

    const pluginName = "chameleonSystemSidebarMenu";

    function Plugin(baseElement) {
        this.$baseElement = $(baseElement);
        this.init();
    }
    $.extend(Plugin.prototype, {
        init: function () {
            const self = this,
                  filterElement = this.$baseElement.find('.sidebar-filter-input'),
                  sidebarMinimizerElement = this.$baseElement.find('.sidebar-minimizer')
            ;
            filterElement.on('keyup', self.filter.bind(this));
            sidebarMinimizerElement.on('click', self.onSidebarToggle.bind(this));

            $.extend($.expr[':'], {
                'chameleonContainsCaseInsensitive': function(elem, i, match, array) {
                    return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
                }
            });
        },
        filter: function (event) {
            const searchTerm = event.target.value;
            if ('' === searchTerm) {
                this.$navTitles.removeClass('d-none');
                this.$navItems.removeClass('d-none');

                return;
            }
            if ('undefined' === typeof this.$navItems) {
                this.$navTitles = this.$baseElement.find('.nav-title');
                this.$navItems = this.$baseElement.find('.nav-item');
            }

            this.$navTitles.addClass('d-none');
            this.$navItems.addClass('d-none');
            const $matchingNavItems = this.$navItems.find(":chameleonContainsCaseInsensitive('" + searchTerm + "')").closest('.nav-item');
            $matchingNavItems.removeClass('d-none');
            // Find title for matching nav items directly after title.
            $matchingNavItems.prev('.nav-title').removeClass('d-none');
            // Find title for further matching nav items.
            $matchingNavItems.prevUntil('.nav-title').prev('.nav-title').removeClass('d-none');
        },
        onSidebarToggle: function () {
            const url = this.$baseElement.data('toggle-notification-url');
            let state;
            // The following condition is inverted, as this handler will be executed before the actual class change.
            if (document.body.classList.contains('sidebar-minimized')) {
                state = 'shown';
            } else {
                state = 'minimized';
            }
            $.post(url, {
                state: state
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
