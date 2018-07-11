If wysiwygPro makes trouble maybe this helps (currently the hack is not implemented, because of structure changes of this widget)

The datepicker didn`t work together with wysiwygpro.
We had to set a delay at the point where the datepicker instance gets appended to the body.

Line: 1362 (end of the script)

/* Initialise the date picker. */
$(document).ready(function() {
  $.datepicker = new Datepicker(); // singleton instance
  // ESONO: need the timeout trick for ie (wysiwyg creates problems otherwise)
  window.setTimeout(function() {
    	$(document.body).append($.datepicker._datepickerDiv).mousedown($.datepicker._checkExternalClick);
    },
    0
  )
});