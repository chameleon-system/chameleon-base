/**
 * @name chameleon_content_filter_diff
 * @description shows a modal with a diff when the editor changes content through filtering
 * @todo optimize modal implementation, create "usable" multi-instance behaviour
 *
 * IMPORTANT:
 * The rendered diff is not 100% correct (new lines and some other parts are not diffed correctly)
 *
 * INSTALLATION:
 * In order to make this plugin work you have to change the "toHtml"-function in "htmldataprocessor.js",
 * as the listened "afterToHtml"-Event is not implemented in the CKEDITOR core.
 *
 * Replace the "return"-statement with the following:
 * <code>
    var _return = editor.fire( 'toHtml', {
        dataValue: data,
        context: context,
        fixForBody: fixForBody,
        dontFilter: !!dontFilter
    } ).dataValue;


    editor.fire('afterToHtml', {
        valueOld: data,
        valueNew: _return
    });

    return _return;
 * </code>
 *
 *
 * @borrows diff-function from http://www.quickdiff.com/
 * @borrows escape-function from http://ejohn.org/projects/javascript-diff-algorithm/
 *
 */

var modalContent = '';
CKEDITOR.plugins.add('chameleon_content_filter_diff', {

    init: function (editor) {
        var self = this;
        editor.on('afterToHtml', function (event) {

            self.diff(event.data.valueOld, event.data.valueNew);

        });
    },
    diff: function(valueOld, valueNew){

        var valueOldOriginal = valueOld,
            valueNewOriginal = valueNew;
        /**
         * lower case everything
         * remove:
         * - \n, \r, \t
         * - replace all whitespaces with nothing
         * so we can get a low level diff
         */
        valueOld = valueOld.toLowerCase().replace("\n", "").replace("\r","").replace("\t","").replace(/\s/g, "");
        valueNew = valueNew.toLowerCase().replace("\n", "").replace("\r","").replace("\t","").replace(/\s/g, "");

        if (valueOld != valueNew){

            var modalId = 'modal_dialog';
            modalContent += '<link href="/chameleon/blackbox/components/ckEditor/plugins/chameleon_content_filter_diff/modal.css" rel="stylesheet" type="text/css"/>';
            modalContent += '<table><thead><tr><th colspan="3">Diff</th></tr></thead><tbody></tbody></table>';
            modalContent = $(modalContent);


            var valueOldOriginalDiff = valueOldOriginal.replace("\n","").replace(/>/g,">\n").replace(/</g,"\n<"),
                valueNewOriginalDiff = valueNewOriginal.replace("\n","").replace(/>/g,">\n").replace(/</g,"\n<");

            var valueOldOriginalDiffArray = valueOldOriginalDiff.split("\n"),
                valueNewOriginalDiffArray = valueNewOriginalDiff.split("\n"),
                valueOldOriginalDiffArrayCleaned = [],
                valueNewOriginalDiffArrayCleaned = [];

            $.map(valueOldOriginalDiffArray, function(item){
                if(item != ''){
                    valueOldOriginalDiffArrayCleaned.push(item);
                }
            });
            $.map(valueNewOriginalDiffArray, function(item){
                if(item != ''){
                    valueNewOriginalDiffArrayCleaned.push(item);
                }
            });

            console.log('DIFF detected');

            console.log(valueOldOriginalDiffArray);
            console.log(valueOldOriginalDiffArrayCleaned);

            console.log(valueNewOriginalDiffArray);
            console.log(valueNewOriginalDiffArrayCleaned);

            diff(valueOldOriginalDiffArrayCleaned, valueNewOriginalDiffArrayCleaned);

            if ($('#'+modalId).length == 0) {
                $('body').append('<div id="'+modalId+'" style="display:none"></div>');
            }

            $('#modal_dialog').dialog({
                width: 600,
                height:500,
                title: 'WYSIWYG Content Diff',
                modal: true,
                position:"center",
                resizable: true,
                draggable: true,
                close:function (event, ui) {
                    //CloseModalIFrameDialog();
                },
                open:function (event, ui) {
                    /*if (!hasCloseButton) {
                        $(event.currentTarget).find('.ui-dialog-titlebar-close').css('display', 'none');
                    }*/
                }
            }).html(modalContent);

        }
        else {
            console.log('NO DIFF');
        }

    }
});

function maakRij(x, y, type, rij){

    var trClassName = '';

    if(type === '+'){
        trClassName = 'add';
    } else if(type === '-'){
        trClassName = 'del';
    }

    modalContent.find('tbody').append('<tr class="' + trClassName + '">' +
            '<td class="codekolom">' + x + '</td>' +
            '<td class="codekolom">' + y + '</td>' +
            '<td class="bredecode">'+ type + ' ' + escape(rij) + '</td>' +
        '</tr>')

    /*
    var tr = document.createElement('tr');
    if(type==='+'){
        tr.className='add';
    } else if(type==='-'){
        tr.className='del';
    }

    var td1 = document.createElement('td');
    var td2 = document.createElement('td');
    var td3 = document.createElement('td');

    td1.className = 'codekolom';
    td2.className = 'codekolom';
    td3.className = 'bredecode';

    var txt1 = document.createTextNode(y);
    var txt2 = document.createTextNode(x);
    var txt3 = document.createTextNode(type + ' ' + rij);

    td1.appendChild(txt1);
    td2.appendChild(txt2);
    td3.appendChild(txt3);

    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);

    tableBody.appendChild(tr);*/
}

function getDiff(matrix, a1, a2, x, y){
    if(x>0 && y>0 && a1[y-1]===a2[x-1]){
        getDiff(matrix, a1, a2, x-1, y-1);
        maakRij(x, y, ' ', a1[y-1]);
    } else {
        if(x>0 && (y===0 || matrix[y][x-1] >= matrix[y-1][x])){
            getDiff(matrix, a1, a2, x-1, y);
            maakRij(x, '', '+', a2[x-1]);
        } else if(y>0 && (x===0 || matrix[y][x-1] < matrix[y-1][x])){
            getDiff(matrix, a1, a2, x, y-1);
            maakRij('', y, '-', a1[y-1], '');
        } else {
            return;
        }
    }

}

function diff(a1, a2){
    var matrix = new Array(a1.length+1);

    for(var y=0; y<matrix.length; y++){
        matrix[y] = new Array(a2.length+1);

        for(var x=0; x<matrix[y].length; x++){
            matrix[y][x] = 0;
        }
    }

    for(var y=1; y<matrix.length; y++){
        for(var x=1; x<matrix[y].length; x++){
            if(a1[y-1]===a2[x-1]){
                matrix[y][x] = 1 + matrix[y-1][x-1];
            } else {
                matrix[y][x] = Math.max(matrix[y-1][x], matrix[y][x-1]);
            }
        }
    }

    try {
        getDiff(matrix, a1, a2, x-1, y-1);
    } catch(e){
        alert(e);
    }
}

function escape(s) {
    var n = s;
    n = n.replace(/&/g, "&amp;");
    n = n.replace(/</g, "&lt;");
    n = n.replace(/>/g, "&gt;");
    n = n.replace(/"/g, "&quot;");

    return n;
}