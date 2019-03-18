/*!
 * jQuery blockUI plugin
 * Version 2.57.0-2013.02.17
 * @requires jQuery v1.7 or later
 *
 * Examples at: http://malsup.com/jquery/block/
 * Copyright (c) 2007-2013 M. Alsup
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Thanks to Amir-Hossein Sobhi for some excellent contributions!
 *
 *
 * @deprecated in 6.3
 * Removed the plugin code and left only the blockUI and unblockUI plugin methods as gateway to the bootstrap 4 modal dialog for backwards compatibility.
 */

;(function() {
"use strict";

	function setup($) {
		$.blockUI   = function(opts) { install(window, opts); };
		$.unblockUI = function(opts) { remove(window, opts); };

		function install(el, opts) {
            CHAMELEON.CORE.showProcessingModal();
		}

		// remove the block
		function remove(el, opts) {
            CHAMELEON.CORE.hideProcessingModal();
		}
	}

	if (typeof define === 'function' && define.amd && define.amd.jQuery) {
		define(['jquery'], setup);
	} else {
		setup(jQuery);
	}

})();
