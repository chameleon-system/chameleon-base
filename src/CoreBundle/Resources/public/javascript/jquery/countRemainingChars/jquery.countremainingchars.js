/*
 * This file is part of the Chameleon System (www.chameleon-system.com).
 *
 * (c) ESONO AG (www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function($) {
    $.fn.extend({
        countRemainingChars: function(maxLength, targetElementSelector) {
            $(this).on("input", { maxLength: maxLength, targetElement: $(targetElementSelector) }, function(event) {
                event.data.targetElement.html(event.data.maxLength - this.value.length);
            });
        }
    });
})(jQuery);