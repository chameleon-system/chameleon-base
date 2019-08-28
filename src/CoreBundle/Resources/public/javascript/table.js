function setPointer(theRow, thePointerColor) {
    if (thePointerColor == '' || typeof(theRow.style) == 'undefined') return false;
    var theCells = '';
    if (typeof(document.getElementsByTagName) != 'undefined') theCells = theRow.getElementsByTagName('td');
    else if (typeof(theRow.cells) != 'undefined') theCells = theRow.cells;
    else return false;
    var rowCellsCnt = theCells.length;
    for (var c = 0; c < rowCellsCnt; c++) {
        theCells[c].style.backgroundColor = thePointerColor;
    }
    return true;
}

// open a record in the list for normal edit function
function EditRecordInList(id) {
    document.cmsform.id.value = id;
    document.cmsform.submit();
}

// open a record in the list for normal edit function as component / popup mode
function EditRecordAsComponent(id) {
    document.getElementById('cmsform').target = '_self';
    document.cmsform.id.value = id;
    document.cmsform.pagedef.value = 'tableeditorPopup';
    document.cmsform.submit();
}

// open the template engine for page "id"
function OpenTemplateEngine(id) {
    document.cmsform.id.value = id;
    document.cmsform.pagedef.value = 'templateengine';
    document.cmsform.submit();
}

function selectExtendedLookupRecord(id) {
    document.cmsformAjaxCall.id.value = id;
    var hiddenField = parent.document.getElementById(document.cmsformAjaxCall.fieldName.value);
    hiddenField.value = id;
    document.cmsformAjaxCall._fnc.value = 'GetDisplayValue';
    PostAjaxForm('cmsformAjaxCall', selectExtendedLookupRecordClose);
}

function selectExtendedLookupMultiTableRecord(id) {
    document.cmsformAjaxCall.id.value = id;
    var hiddenField = parent.document.getElementById(document.cmsformAjaxCall.fieldName.value);
    hiddenField.value = id;
    var hiddenFieldTableName = parent.document.getElementById(document.cmsformAjaxCall.fieldName.value + '_table_name');
    hiddenFieldTableName.value = document.cmsformAjaxCall.tableid.value;
    document.cmsformAjaxCall._fnc.value = 'GetDisplayValue';
    PostAjaxForm('cmsformAjaxCall', selectExtendedLookupMultiTableRecordClose);
}

function selectExtendedLookupRecordClose(data, statusText) {
    var fieldDisplayValue = parent.document.getElementById(document.cmsformAjaxCall.fieldName.value + 'CurrentSelection');
    fieldDisplayValue.innerHTML = data;
    parent.CloseModalIFrameDialog();
}

function selectExtendedLookupMultiTableRecordClose(data, statusText) {
    var fieldDisplayValue = parent.document.getElementById(document.cmsformAjaxCall.fieldName.value + 'CurrentSelection');
    var hiddenFieldTableName = parent.document.getElementById('aTableNames[' + document.cmsformAjaxCall.tableid.value + ']');
    fieldDisplayValue.innerHTML = hiddenFieldTableName.value + ' - ' + data;
    parent.CloseModalIFrameDialog();
}

function DeleteRecord(id) {
    var message = CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.confirm_delete');

    if (confirm(message)) {
        CHAMELEON.CORE.showProcessingModal();
        document.cmsformdel.elements['module_fnc[contentmodule]'].value = 'Delete';
        document.cmsformdel.id.value = id;
        document.cmsformdel.submit();
    }
}


function DeleteSelectedRecords(formName) {
    var IDs = '';
    $("input[name='aInputIdList[]']").each(function () {
            if (this.checked) {
                if (IDs != '') IDs = IDs + ',';
                IDs = IDs + this.value;
            }
        }
    );

    if (IDs != '') {
        var message = CHAMELEON.CORE.i18n.Translate("chameleon_system_core.js.confirm_multi_delete");
        if (confirm(message)) {
            document.cmsformworkonlist.elements['module_fnc[contentmodule]'].value = 'DeleteSelected';
            document.cmsformworkonlist.items.value = IDs;
            document.cmsformworkonlist.submit();
        }
    } else {
        toasterMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.error_no_selection'), 'WARNING');
    }
}


function ChangeListMarking(fieldValue, formName) {
    var searchId = "#" + formName;

    $("input[name='aInputIdList[]']").each(function () {
            this.checked = fieldValue;
        }
    );
}

/**
 * force links inside table cells to fixed height based on TR height
 */
$(document).ready(function () {
    CHAMELEON.CORE.handleFormAndLinkTargetsInModals();

    $('.TCMSListManagerFullGroupTable tr.TGroupTableItemRow').each(function () {
        var TRHeight = $(this).innerHeight() - 13; // - TD padding and borders
        $(this).find('a.TGroupTableLink').css('height', TRHeight);
    });

    $('[data-select2-option]').each(function () {
        $(this).select2($(this).data('select2-option'));
    });

    $('.submitOnSelect').on('select2:select', function (e) {
        $(this).closest('form').submit();
    });

    function createSearchWordInputWithValue(value){
        const listname = searchLookup.data('listname');

        if ('' === listname) {
            return;
        }

        const input = document.createElement('input');

        input.setAttribute('type', 'hidden');
        input.setAttribute('name', '_search_word');
        if ('string' === typeof value && '' !== value) {
            input.setAttribute('value', value);
        }

        document[listname].appendChild(input);
        document[listname]._startRecord.value=0;

        return document[listname];
    }

    var searchLookup = $('select#searchLookup');

    let uiEnabled = true;

    searchLookup.select2({
        placeholder: searchLookup.data('select2-placeholder'),
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: searchLookup.data('select2-ajax'),
            dataType: 'json',
            processResults: function (data) {
                return {
                    results: JSON.parse(data)
                };
            }
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function (data, container) {
            // transfer class to select2 element
            if (data.cssClass && '' !== data.cssClass) {
                $(container).addClass(data.cssClass);
            }

            return data.html;
        },
        tags: true,
        createTag: function (params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            }
        }
    }).on('select2:clear', function(e) {
        const form = createSearchWordInputWithValue();

        uiEnabled = false;
        e.stopPropagation();
        form.submit();

    }).on('select2:closing', function (e) {
        if (false === uiEnabled) {
            return;
        }
        if (false === hasEventData(e)) {
            return;
        }

        const data = getDataFromEvent(e);
        const searchField = document.querySelector('.select2-search__field');

        if (isSelected(data)) {
            switchRecord(data.id.trim(), data.text.trim());
        } else {
            let searchTerm = '';

            if (isSearchTermEmpty(data)) {
                if (searchFieldContainsValue(searchField)) {
                    searchTerm = getValueFromSearchField(searchField);
                }
            } else {
                searchTerm = getSearchTerm(data);
            }

            if ('' === searchTerm) {
                return;
            }

            const form = createSearchWordInputWithValue(searchTerm);

            form.submit();
        }

    }).on('select2:open', function(e) {
        /* Synchronize select2's search term display field by listening to keyboard events. */
        const select2SearchField = document.querySelector('.select2-search__field');
        const select2 = getSibling(this, '.select2');
        const selectSelectionRendered = null === select2 ? null : select2.querySelector('.select2-selection__rendered');

        if (null === select2SearchField || null === selectSelectionRendered) {
            return;
        }

        select2SearchField.value = $(selectSelectionRendered).contents().filter(function() {
            return this.nodeType === Node.TEXT_NODE;
        }).text();

        select2SearchField.select();

        $(select2SearchField).off('input');

        if ('1' === this.dataset.keyUpListenerAttached) {
            return;
        }
        select2SearchField.addEventListener('keyup', (e) => {
            /* Preserve clear button */
            const select2SelectionClear = selectSelectionRendered.querySelector('.select2-selection__clear');
            selectSelectionRendered.title = select2SearchField.value;
            selectSelectionRendered.textContent = select2SearchField.value;
            if (null !== select2SelectionClear) {
                selectSelectionRendered.appendChild(select2SelectionClear);
            }
        });

        this.dataset.keyUpListenerAttached = '1';
    });

    function hasEventData(e) {
        const argOriginalSelect2Event = e.params.args.originalSelect2Event;

        if ('undefined' === typeof argOriginalSelect2Event) {
            return false;
        }

        const data = argOriginalSelect2Event.data;

        return 'undefined' !== typeof data;
    }

    function getDataFromEvent(e) {
        return  e.params.args.originalSelect2Event.data;
    }

    function isSelected(originalSelect2EventData) {
        if (true === originalSelect2EventData.newOption) {
            return false;
        }

        if ('string' !== typeof originalSelect2EventData.id) {
            return false;
        }

        if (originalSelect2EventData.id.trim() === originalSelect2EventData.text.trim()) {
            return false;
        }

        return '' !== originalSelect2EventData.id.trim();
    }

    function isSearchTermEmpty(originalSelect2EventData) {
        return '' === originalSelect2EventData.text.trim();
    }

    function getValueFromSearchField(searchField) {
        if (null === searchField) {
            return '';
        }

        return searchField.value;
    }

    function getSearchTerm(originalSelect2EventData) {
        return originalSelect2EventData.text.trim();
    }

    function searchFieldContainsValue(searchField) {
        if (null === searchField) {
            return false;
        }

        const value = searchField.value;
        if ('' === value) {
            return false;
        }

        return value;
    }

    function getSibling(node, selector) {
        let sibling = node.nextSibling;

        if (null === sibling) {
            return null;
        }

        try {
            if (sibling.matches(selector)) {
                return sibling;
            }
        } catch (ex) {
            // go on with recursion
        }

        return getSibling(sibling, selector);
    }

    function switchRecord(id, searchTerm = '') {
        if ('' !== id) {
            const searchTermPart = '' !== searchTerm ? `&_search_word=${searchTerm}` : '';
            /* Also submit the search term (the selected suggestion of select2)
             * so it can be added to the session as last search term.
             * */
            top.document.location.href = searchLookup.data('record-url') + '&id=' + id + searchTermPart;
        }
    }
});