/*!
 * TOAST UI Editor : Table Merged Cell Plugin
 * @version 3.0.2 | Thu Feb 10 2022
 * @author NHN FE Development Lab <dl_javascript@nhn.com>
 * @license MIT
 */
(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["toastui"] = factory();
	else
		root["toastui"] = root["toastui"] || {}, root["toastui"]["Editor"] = root["toastui"]["Editor"] || {}, root["toastui"]["Editor"]["plugin"] = root["toastui"]["Editor"]["plugin"] || {}, root["toastui"]["Editor"]["plugin"]["tableMergedCell"] = factory();
})(self, function() {
return /******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 893:
/***/ (function(module) {

/**
 * @fileoverview Execute the provided callback once for each element present in the array(or Array-like object) in ascending order.
 * @author NHN FE Development Lab <dl_javascript@nhn.com>
 */



/**
 * Execute the provided callback once for each element present
 * in the array(or Array-like object) in ascending order.
 * If the callback function returns false, the loop will be stopped.
 * Callback function(iteratee) is invoked with three arguments:
 *  1) The value of the element
 *  2) The index of the element
 *  3) The array(or Array-like object) being traversed
 * @param {Array|Arguments|NodeList} arr The array(or Array-like object) that will be traversed
 * @param {function} iteratee Callback function
 * @param {Object} [context] Context(this) of callback function
 * @memberof module:collection
 * @example
 * // ES6
 * import forEachArray from 'tui-code-snippet/collection/forEachArray';
 * 
 * // CommonJS
 * const forEachArray = require('tui-code-snippet/collection/forEachArray'); 
 *
 * let sum = 0;
 *
 * forEachArray([1,2,3], function(value){
 *   sum += value;
 * });
 * alert(sum); // 6
 */
function forEachArray(arr, iteratee, context) {
  var index = 0;
  var len = arr.length;

  context = context || null;

  for (; index < len; index += 1) {
    if (iteratee.call(context, arr[index], index, arr) === false) {
      break;
    }
  }
}

module.exports = forEachArray;


/***/ }),

/***/ 990:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

/**
 * @fileoverview Transform the Array-like object to Array.
 * @author NHN FE Development Lab <dl_javascript@nhn.com>
 */



var forEachArray = __webpack_require__(893);

/**
 * Transform the Array-like object to Array.
 * In low IE (below 8), Array.prototype.slice.call is not perfect. So, try-catch statement is used.
 * @param {*} arrayLike Array-like object
 * @returns {Array} Array
 * @memberof module:collection
 * @example
 * // ES6
 * import toArray from 'tui-code-snippet/collection/toArray'; 
 * 
 * // CommonJS
 * const toArray = require('tui-code-snippet/collection/toArray'); 
 *
 * const arrayLike = {
 *   0: 'one',
 *   1: 'two',
 *   2: 'three',
 *   3: 'four',
 *   length: 4
 * };
 * const result = toArray(arrayLike);
 *
 * alert(result instanceof Array); // true
 * alert(result); // one,two,three,four
 */
function toArray(arrayLike) {
  var arr;
  try {
    arr = Array.prototype.slice.call(arrayLike);
  } catch (e) {
    arr = [];
    forEachArray(arrayLike, function(value) {
      arr.push(value);
    });
  }

  return arr;
}

module.exports = toArray;


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "default": function() { return /* binding */ tableMergedCellPlugin; }
});

;// CONCATENATED MODULE: ./src/markdown/parser.ts
function getSpanInfo(content, type, oppositeType) {
    var reSpan = new RegExp("^((?:" + oppositeType + "=[0-9]+:)?)" + type + "=([0-9]+):(.*)");
    var parsed = reSpan.exec(content);
    var spanCount = 1;
    if (parsed) {
        spanCount = parseInt(parsed[2], 10);
        content = parsed[1] + parsed[3];
    }
    return [spanCount, content];
}
function extendTableCellIndexWithRowspanMap(node, parent, rowspan) {
    var prevRow = parent.prev;
    if (prevRow) {
        var columnLen = parent.parent.parent.columns.length;
        // increment the index when prev row has the rowspan count.
        for (var i = node.startIdx; i < columnLen; i += 1) {
            var prevRowspanCount = prevRow.rowspanMap[i];
            if (prevRowspanCount && prevRowspanCount > 1) {
                parent.rowspanMap[i] = prevRowspanCount - 1;
                if (i <= node.endIdx) {
                    node.startIdx += 1;
                    node.endIdx += 1;
                }
            }
        }
    }
    if (rowspan > 1) {
        var startIdx = node.startIdx, endIdx = node.endIdx;
        for (var i = startIdx; i <= endIdx; i += 1) {
            parent.rowspanMap[i] = rowspan;
        }
    }
}
var markdownParsers = {
    // @ts-expect-error
    tableRow: function (node, _a) {
        var entering = _a.entering;
        if (entering) {
            node.rowspanMap = {};
            if (node.prev && !node.firstChild) {
                var prevRowspanMap_1 = node.prev.rowspanMap;
                Object.keys(prevRowspanMap_1).forEach(function (key) {
                    if (prevRowspanMap_1[key] > 1) {
                        node.rowspanMap[key] = prevRowspanMap_1[key] - 1;
                    }
                });
            }
        }
    },
    // @ts-expect-error
    tableCell: function (node, _a) {
        var _b, _c;
        var entering = _a.entering;
        var parent = node.parent, prev = node.prev, stringContent = node.stringContent;
        if (entering) {
            var attrs = {};
            var content = stringContent;
            var _d = [1, 1], colspan = _d[0], rowspan = _d[1];
            _b = getSpanInfo(content, '@cols', '@rows'), colspan = _b[0], content = _b[1];
            _c = getSpanInfo(content, '@rows', '@cols'), rowspan = _c[0], content = _c[1];
            node.stringContent = content;
            if (prev) {
                node.startIdx = prev.endIdx + 1;
                node.endIdx = node.startIdx;
            }
            if (colspan > 1) {
                attrs.colspan = colspan;
                node.endIdx += colspan - 1;
            }
            if (rowspan > 1) {
                attrs.rowspan = rowspan;
            }
            node.attrs = attrs;
            extendTableCellIndexWithRowspanMap(node, parent, rowspan);
            var tablePart = parent.parent;
            if (tablePart.type === 'tableBody' && node.endIdx >= tablePart.parent.columns.length) {
                node.ignored = true;
            }
        }
    },
};

;// CONCATENATED MODULE: ./src/markdown/renderer.ts
var __assign = (undefined && undefined.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var toHTMLRenderers = {
    // @ts-ignore
    tableRow: function (node, _a) {
        var entering = _a.entering, origin = _a.origin;
        if (entering) {
            return origin();
        }
        var result = [];
        if (node.lastChild) {
            var columnLen = node.parent.parent.columns.length;
            var lastColIdx = node.lastChild.endIdx;
            for (var i = lastColIdx + 1; i < columnLen; i += 1) {
                if (!node.prev || !node.prev.rowspanMap[i] || node.prev.rowspanMap[i] <= 1) {
                    result.push({
                        type: 'openTag',
                        tagName: 'td',
                        outerNewLine: true,
                    }, {
                        type: 'closeTag',
                        tagName: 'td',
                        outerNewLine: true,
                    });
                }
            }
        }
        result.push({
            type: 'closeTag',
            tagName: 'tr',
            outerNewLine: true,
        });
        return result;
    },
    // @ts-ignore
    tableCell: function (node, _a) {
        var entering = _a.entering, origin = _a.origin;
        var result = origin();
        if (node.ignored) {
            return result;
        }
        if (entering) {
            var attributes = __assign({}, node.attrs);
            result.attributes = __assign(__assign({}, result.attributes), attributes);
        }
        return result;
    },
};

;// CONCATENATED MODULE: ./src/wysiwyg/renderer.ts
var DELIM_LENGH = 3;
function repeat(text, count) {
    var result = '';
    for (var i = 0; i < count; i += 1) {
        result += text;
    }
    return result;
}
function createTableHeadDelim(textContent, columnAlign) {
    var textLen = textContent.length;
    var leftDelim = '';
    var rightDelim = '';
    if (columnAlign === 'left') {
        leftDelim = ':';
        textLen -= 1;
    }
    else if (columnAlign === 'right') {
        rightDelim = ':';
        textLen -= 1;
    }
    else if (columnAlign === 'center') {
        leftDelim = ':';
        rightDelim = ':';
        textLen -= 2;
    }
    return "" + leftDelim + repeat('-', Math.max(textLen, DELIM_LENGH)) + rightDelim;
}
function createDelim(node) {
    var _a = node.attrs, rowspan = _a.rowspan, colspan = _a.colspan;
    var spanInfo = '';
    if (rowspan) {
        spanInfo = "@rows=" + rowspan + ":";
    }
    if (colspan) {
        spanInfo = "@cols=" + colspan + ":" + spanInfo;
    }
    return { delim: "| " + spanInfo };
}
var toMarkdownRenderers = {
    tableHead: function (nodeInfo) {
        var row = nodeInfo.node.firstChild;
        var delim = '';
        if (row) {
            row.forEach(function (_a) {
                var textContent = _a.textContent, attrs = _a.attrs;
                var headDelim = createTableHeadDelim(textContent, attrs.align);
                delim += "| " + headDelim + " ";
                if (attrs.colspan) {
                    for (var i = 0; i < attrs.colspan - 1; i += 1) {
                        delim += "| " + headDelim + " ";
                    }
                }
            });
        }
        return { delim: delim };
    },
    tableHeadCell: function (nodeInfo) {
        return createDelim(nodeInfo.node);
    },
    tableBodyCell: function (nodeInfo) {
        return createDelim(nodeInfo.node);
    },
};

;// CONCATENATED MODULE: ./src/i18n/langs.ts
function addLangs(i18n) {
    i18n.setLanguage(['ko', 'ko-KR'], {
        'Merge cells': '셀 병합',
        'Split cells': '셀 병합해제',
        'Cannot change part of merged cell': '병합된 셀의 일부를 변경할 수 없습니다.',
        'Cannot paste row merged cells into the table header': '테이블 헤더에는 행 병합된 셀을 붙여넣을 수 없습니다.',
    });
    i18n.setLanguage(['en', 'en-US'], {
        'Merge cells': 'Merge cells',
        'Split cells': 'Split cells',
        'Cannot change part of merged cell': 'Cannot change part of merged cell.',
        'Cannot paste row merged cells into the table header': 'Cannot paste row merged cells into the table header.',
    });
    i18n.setLanguage(['es', 'es-ES'], {
        'Merge cells': 'Combinar celdas',
        'Split cells': 'Separar celdas',
        'Cannot change part of merged cell': 'No se puede cambiar parte de una celda combinada.',
        'Cannot paste row merged cells into the table header': 'No se pueden pegar celdas combinadas en el encabezado de tabla.',
    });
    i18n.setLanguage(['ja', 'ja-JP'], {
        'Merge cells': 'セルの結合',
        'Split cells': 'セルの結合を解除',
        'Cannot change part of merged cell': '結合されたセルの一部を変更することはできません。',
        'Cannot paste row merged cells into the table header': '行にマージされたセルをヘッダーに貼り付けることはできません。',
    });
    i18n.setLanguage(['nl', 'nl-NL'], {
        'Merge cells': 'Cellen samenvoegen',
        'Split cells': 'Samengevoegde cellen ongedaan maken',
        'Cannot change part of merged cell': 'Kan geen deel uit van een samengevoegde cel veranderen.',
        'Cannot paste row merged cells into the table header': 'Kan geen rij met samengevoegde cellen in de koptekst plakken.',
    });
    i18n.setLanguage('zh-CN', {
        'Merge cells': '合并单元格',
        'Split cells': '取消合并单元格',
        'Cannot change part of merged cell': '无法更改合并单元格的一部分。',
        'Cannot paste row merged cells into the table header': '无法将行合并单元格粘贴到标题中。',
    });
    i18n.setLanguage(['de', 'de-DE'], {
        'Merge cells': 'Zellen zusammenführen',
        'Split cells': 'Zusammenführen rückgängig machen',
        'Cannot change part of merged cell': 'Der Teil der verbundenen Zelle kann nicht geändert werden.',
        'Cannot paste row merged cells into the table header': 'Die Zeile der verbundenen Zellen kann nicht in die Kopfzeile eingefügt werden.',
    });
    i18n.setLanguage(['ru', 'ru-RU'], {
        'Merge cells': 'Объединить ячейки',
        'Split cells': 'Разъединить ячейки',
        'Cannot change part of merged cell': 'Вы не можете изменять часть комбинированной ячейки.',
        'Cannot paste row merged cells into the table header': 'Вы не можете вставлять объединенные ячейки в заголовок таблицы.',
    });
    i18n.setLanguage(['fr', 'fr-FR'], {
        'Merge cells': 'Fusionner les cellules',
        'Split cells': 'Séparer les cellules',
        'Cannot change part of merged cell': 'Impossible de modifier une partie de la cellule fusionnée.',
        'Cannot paste row merged cells into the table header': "Impossible de coller les cellules fusionnées dans l'en-tête du tableau.",
    });
    i18n.setLanguage(['uk', 'uk-UA'], {
        'Merge cells': "Об'єднати комірки",
        'Split cells': "Роз'єднати комірки",
        'Cannot change part of merged cell': 'Ви не можете змінювати частину комбінованої комірки.',
        'Cannot paste row merged cells into the table header': "Ви не можете вставляти об'єднані комірки в заголовок таблиці.",
    });
    i18n.setLanguage(['tr', 'tr-TR'], {
        'Merge cells': 'Hücreleri birleştir',
        'Split cells': 'Hücreleri ayır',
        'Cannot change part of merged cell': 'Birleştirilmiş hücrelerin bir kısmı değiştirelemez.',
        'Cannot paste row merged cells into the table header': 'Satırda birleştirilmiş hücreler sütun başlığına yapıştırılamaz',
    });
    i18n.setLanguage(['fi', 'fi-FI'], {
        'Merge cells': 'Yhdistä solut',
        'Split cells': 'Jaa solut',
        'Cannot change part of merged cell': 'Yhdistettyjen solujen osaa ei voi muuttaa',
        'Cannot paste row merged cells into the table header': 'Soluja ei voi yhdistää taulukon otsikkoriviin',
    });
    i18n.setLanguage(['cs', 'cs-CZ'], {
        'Merge cells': 'Spojit buňky',
        'Split cells': 'Rozpojit buňky',
        'Cannot change part of merged cell': 'Nelze měnit část spojené buňky',
        'Cannot paste row merged cells into the table header': 'Nelze vkládat spojené buňky do záhlaví tabulky',
    });
    i18n.setLanguage('ar', {
        'Merge cells': 'دمج الوحدات',
        'Split cells': 'إلغاء دمج الوحدات',
        'Cannot change part of merged cell': 'لا يمكن تغيير جزء من الخلية المدموجة',
        'Cannot paste row merged cells into the table header': 'لا يمكن لصق الخلايا المدموجة من صف واحد في رأس الجدول',
    });
    i18n.setLanguage(['pl', 'pl-PL'], {
        'Merge cells': 'Scal komórki',
        'Split cells': 'Rozłącz komórki',
        'Cannot change part of merged cell': 'Nie można zmienić części scalonej komórki.',
        'Cannot paste row merged cells into the table header': 'Nie można wkleić komórek o scalonym rzędzie w nagłówek tabeli.',
    });
    i18n.setLanguage('zh-TW', {
        'Merge cells': '合併儲存格',
        'Split cells': '取消合併儲存格',
        'Cannot change part of merged cell': '無法變更儲存格的一部分。',
        'Cannot paste row merged cells into the table header': '無法將合併的儲存格貼上至表格標題中。',
    });
    i18n.setLanguage(['gl', 'gl-ES'], {
        'Merge cells': 'Combinar celas',
        'Split cells': 'Separar celas',
        'Cannot change part of merged cell': 'Non se pode cambiar parte dunha cela combinada',
        'Cannot paste row merged cells into the table header': 'Non se poden pegar celas no encabezado da táboa',
    });
    i18n.setLanguage(['sv', 'sv-SE'], {
        'Merge cells': 'Sammanfoga celler',
        'Split cells': 'Dela celler',
        'Cannot change part of merged cell': 'Ej möjligt att ändra en del av en sammanfogad cell',
        'Cannot paste row merged cells into the table header': 'Ej möjligt att klistra in rad-sammanfogade celler i tabellens huvud',
    });
    i18n.setLanguage(['it', 'it-IT'], {
        'Merge cells': 'Unisci celle',
        'Split cells': 'Separa celle',
        'Cannot change part of merged cell': 'Non è possibile modificare parte di una cella unita',
        'Cannot paste row merged cells into the table header': "Non è possibile incollare celle unite per riga nell'intestazione della tabella",
    });
    i18n.setLanguage(['nb', 'nb-NO'], {
        'Merge cells': 'Slå sammen celler',
        'Split cells': 'Separer celler',
        'Cannot change part of merged cell': 'Kan ikke endre deler av sammenslåtte celler',
        'Cannot paste row merged cells into the table header': 'Kan ikke lime inn rad med sammenslåtte celler',
    });
    i18n.setLanguage(['hr', 'hr-HR'], {
        'Merge cells': 'Spoji ćelije',
        'Split cells': 'Odspoji ćelije',
        'Cannot change part of merged cell': 'Ne mogu mijenjati dio spojene ćelije.',
        'Cannot paste row merged cells into the table header': 'Ne mogu zaljepiti redak spojenih ćelija u zaglavlje tablice',
    });
}

;// CONCATENATED MODULE: ./src/wysiwyg/tableOffsetMapMixin.ts
var tableOffsetMapMixin_assign = (undefined && undefined.__assign) || function () {
    tableOffsetMapMixin_assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return tableOffsetMapMixin_assign.apply(this, arguments);
};
var offsetMapMixin = {
    extendedRowspan: function (rowIdx, colIdx) {
        var rowspanInfo = this.rowInfo[rowIdx].rowspanMap[colIdx];
        return !!rowspanInfo && rowspanInfo.startSpanIdx !== rowIdx;
    },
    extendedColspan: function (rowIdx, colIdx) {
        var colspanInfo = this.rowInfo[rowIdx].colspanMap[colIdx];
        return !!colspanInfo && colspanInfo.startSpanIdx !== colIdx;
    },
    getRowspanCount: function (rowIdx, colIdx) {
        var rowspanInfo = this.rowInfo[rowIdx].rowspanMap[colIdx];
        return rowspanInfo ? rowspanInfo.count : 0;
    },
    getColspanCount: function (rowIdx, colIdx) {
        var colspanInfo = this.rowInfo[rowIdx].colspanMap[colIdx];
        return colspanInfo ? colspanInfo.count : 0;
    },
    decreaseColspanCount: function (rowIdx, colIdx) {
        var colspanInfo = this.rowInfo[rowIdx].colspanMap[colIdx];
        var startColspanInfo = this.rowInfo[rowIdx].colspanMap[colspanInfo.startSpanIdx];
        startColspanInfo.count -= 1;
        return startColspanInfo.count;
    },
    decreaseRowspanCount: function (rowIdx, colIdx) {
        var rowspanInfo = this.rowInfo[rowIdx].rowspanMap[colIdx];
        var startRowspanInfo = this.rowInfo[rowspanInfo.startSpanIdx].rowspanMap[colIdx];
        startRowspanInfo.count -= 1;
        return startRowspanInfo.count;
    },
    getColspanStartInfo: function (rowIdx, colIdx) {
        var colspanMap = this.rowInfo[rowIdx].colspanMap;
        var colspanInfo = colspanMap[colIdx];
        if (colspanInfo) {
            var startSpanIdx = colspanInfo.startSpanIdx;
            var cellInfo = this.rowInfo[rowIdx][startSpanIdx];
            return {
                node: this.table.nodeAt(cellInfo.offset - this.tableStartOffset),
                pos: cellInfo.offset,
                startSpanIdx: startSpanIdx,
                count: colspanMap[startSpanIdx].count,
            };
        }
        return null;
    },
    getRowspanStartInfo: function (rowIdx, colIdx) {
        var rowspanMap = this.rowInfo[rowIdx].rowspanMap;
        var rowspanInfo = rowspanMap[colIdx];
        if (rowspanInfo) {
            var startSpanIdx = rowspanInfo.startSpanIdx;
            var cellInfo = this.rowInfo[startSpanIdx][colIdx];
            return {
                node: this.table.nodeAt(cellInfo.offset - this.tableStartOffset),
                pos: cellInfo.offset,
                startSpanIdx: startSpanIdx,
                count: this.rowInfo[startSpanIdx].rowspanMap[colIdx].count,
            };
        }
        return null;
    },
    getSpannedOffsets: function (selectionInfo) {
        var startRowIdx = selectionInfo.startRowIdx, startColIdx = selectionInfo.startColIdx, endRowIdx = selectionInfo.endRowIdx, endColIdx = selectionInfo.endColIdx;
        for (var rowIdx = endRowIdx; rowIdx >= startRowIdx; rowIdx -= 1) {
            if (this.rowInfo[rowIdx]) {
                var _a = this.rowInfo[rowIdx], rowspanMap = _a.rowspanMap, colspanMap = _a.colspanMap;
                for (var colIdx = endColIdx; colIdx >= startColIdx; colIdx -= 1) {
                    var rowspanInfo = rowspanMap[colIdx];
                    var colspanInfo = colspanMap[colIdx];
                    if (rowspanInfo) {
                        startRowIdx = Math.min(startRowIdx, rowspanInfo.startSpanIdx);
                    }
                    if (colspanInfo) {
                        startColIdx = Math.min(startColIdx, colspanInfo.startSpanIdx);
                    }
                }
            }
        }
        for (var rowIdx = startRowIdx; rowIdx <= endRowIdx; rowIdx += 1) {
            if (this.rowInfo[rowIdx]) {
                var _b = this.rowInfo[rowIdx], rowspanMap = _b.rowspanMap, colspanMap = _b.colspanMap;
                for (var colIdx = startColIdx; colIdx <= endColIdx; colIdx += 1) {
                    var rowspanInfo = rowspanMap[colIdx];
                    var colspanInfo = colspanMap[colIdx];
                    if (rowspanInfo) {
                        endRowIdx = Math.max(endRowIdx, rowIdx + rowspanInfo.count - 1);
                    }
                    if (colspanInfo) {
                        endColIdx = Math.max(endColIdx, colIdx + colspanInfo.count - 1);
                    }
                }
            }
        }
        return { startRowIdx: startRowIdx, startColIdx: startColIdx, endRowIdx: endRowIdx, endColIdx: endColIdx };
    },
};
function extendPrevRowspan(prevRowInfo, rowInfo) {
    var rowspanMap = rowInfo.rowspanMap, colspanMap = rowInfo.colspanMap;
    var prevRowspanMap = prevRowInfo.rowspanMap, prevColspanMap = prevRowInfo.colspanMap;
    Object.keys(prevRowspanMap).forEach(function (key) {
        var colIdx = Number(key);
        var prevRowspanInfo = prevRowspanMap[colIdx];
        if ((prevRowspanInfo === null || prevRowspanInfo === void 0 ? void 0 : prevRowspanInfo.count) > 1) {
            var prevColspanInfo = prevColspanMap[colIdx];
            var count = prevRowspanInfo.count, startSpanIdx = prevRowspanInfo.startSpanIdx;
            rowspanMap[colIdx] = { count: count - 1, startSpanIdx: startSpanIdx };
            colspanMap[colIdx] = prevColspanInfo;
            rowInfo[colIdx] = tableOffsetMapMixin_assign(tableOffsetMapMixin_assign({}, prevRowInfo[colIdx]), { extended: true });
            rowInfo.length += 1;
        }
    });
}
function extendPrevColspan(rowspan, colspan, rowIdx, colIdx, rowInfo) {
    var rowspanMap = rowInfo.rowspanMap, colspanMap = rowInfo.colspanMap;
    for (var i = 1; i < colspan; i += 1) {
        colspanMap[colIdx + i] = { count: colspan - i, startSpanIdx: colIdx };
        if (rowspan > 1) {
            rowspanMap[colIdx + i] = { count: rowspan, startSpanIdx: rowIdx };
        }
        rowInfo[colIdx + i] = tableOffsetMapMixin_assign({}, rowInfo[colIdx]);
        rowInfo.length += 1;
    }
}
var createOffsetMapMixin = function (headOrBody, startOffset, startFromBody) {
    if (startFromBody === void 0) { startFromBody = false; }
    var cellInfoMatrix = [];
    var beInBody = headOrBody.type.name === 'tableBody';
    headOrBody.forEach(function (row, rowOffset, rowIdx) {
        // get row index based on table(not table head or table body)
        var rowIdxInWholeTable = beInBody && !startFromBody ? rowIdx + 1 : rowIdx;
        var prevRowInfo = cellInfoMatrix[rowIdx - 1];
        var rowInfo = { rowspanMap: {}, colspanMap: {}, length: 0 };
        if (prevRowInfo) {
            extendPrevRowspan(prevRowInfo, rowInfo);
        }
        row.forEach(function (_a, cellOffset) {
            var _b, _c;
            var nodeSize = _a.nodeSize, attrs = _a.attrs;
            var colspan = (_b = attrs.colspan) !== null && _b !== void 0 ? _b : 1;
            var rowspan = (_c = attrs.rowspan) !== null && _c !== void 0 ? _c : 1;
            var colIdx = 0;
            while (rowInfo[colIdx]) {
                colIdx += 1;
            }
            rowInfo[colIdx] = {
                // 2 is the sum of the front and back positions of the tag
                offset: startOffset + rowOffset + cellOffset + 2,
                nodeSize: nodeSize,
            };
            rowInfo.length += 1;
            if (rowspan > 1) {
                rowInfo.rowspanMap[colIdx] = { count: rowspan, startSpanIdx: rowIdxInWholeTable };
            }
            if (colspan > 1) {
                rowInfo.colspanMap[colIdx] = { count: colspan, startSpanIdx: colIdx };
                extendPrevColspan(rowspan, colspan, rowIdxInWholeTable, colIdx, rowInfo);
            }
        });
        cellInfoMatrix.push(rowInfo);
    });
    return cellInfoMatrix;
};

// EXTERNAL MODULE: ../../node_modules/tui-code-snippet/collection/toArray.js
var toArray = __webpack_require__(990);
var toArray_default = /*#__PURE__*/__webpack_require__.n(toArray);
;// CONCATENATED MODULE: ./src/wysiwyg/contextMenu.ts

var TABLE_CELL_SELECT_CLASS = '.toastui-editor-cell-selected';
function hasSpanAttr(tableCell) {
    return (Number(tableCell.getAttribute('colspan')) > 1 || Number(tableCell.getAttribute('rowspan')) > 1);
}
function hasSpanningCell(headOrBody) {
    return toArray_default()(headOrBody.querySelectorAll(TABLE_CELL_SELECT_CLASS)).some(hasSpanAttr);
}
function isCellSelected(headOrBody) {
    return !!headOrBody.querySelectorAll(TABLE_CELL_SELECT_CLASS).length;
}
function createMergedTableContextMenu(context, tableCell) {
    var i18n = context.i18n, eventEmitter = context.eventEmitter;
    var headOrBody = tableCell.parentElement.parentElement;
    var mergedTableContextMenu = [];
    if (isCellSelected(headOrBody)) {
        mergedTableContextMenu.push({
            label: i18n.get('Merge cells'),
            onClick: function () { return eventEmitter.emit('command', 'mergeCells'); },
            className: 'merge-cells',
        });
    }
    if (hasSpanAttr(tableCell) || hasSpanningCell(headOrBody)) {
        mergedTableContextMenu.push({
            label: i18n.get('Split cells'),
            onClick: function () { return eventEmitter.emit('command', 'splitCells'); },
            className: 'split-cells',
        });
    }
    return mergedTableContextMenu;
}
function addMergedTableContextMenu(context) {
    context.eventEmitter.listen('contextmenu', function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        var _a = args[0], menuGroups = _a.menuGroups, tableCell = _a.tableCell;
        var mergedTableContextMenu = createMergedTableContextMenu(context, tableCell);
        if (mergedTableContextMenu.length) {
            // add merged table context menu on third group
            menuGroups.splice(2, 0, mergedTableContextMenu);
        }
    });
}

;// CONCATENATED MODULE: ./src/wysiwyg/util.ts
var util_assign = (undefined && undefined.__assign) || function () {
    util_assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return util_assign.apply(this, arguments);
};
function findNodeBy(pos, condition) {
    var depth = pos.depth;
    while (depth >= 0) {
        var node = pos.node(depth);
        if (condition(node, depth)) {
            return {
                node: node,
                depth: depth,
                offset: depth > 0 ? pos.before(depth) : 0,
            };
        }
        depth -= 1;
    }
    return null;
}
function findCell(pos) {
    return findNodeBy(pos, function (_a) {
        var type = _a.type;
        return type.name === 'tableHeadCell' || type.name === 'tableBodyCell';
    });
}
function getResolvedSelection(selection, context) {
    if (selection instanceof context.pmState.TextSelection) {
        var $anchor = selection.$anchor;
        var foundCell = findCell($anchor);
        if (foundCell) {
            var anchor = $anchor.node(0).resolve($anchor.before(foundCell.depth));
            return { anchor: anchor, head: anchor };
        }
    }
    var _a = selection, startCell = _a.startCell, endCell = _a.endCell;
    return { anchor: startCell, head: endCell };
}
function getRowAndColumnCount(_a) {
    var startRowIdx = _a.startRowIdx, startColIdx = _a.startColIdx, endRowIdx = _a.endRowIdx, endColIdx = _a.endColIdx;
    return { rowCount: endRowIdx - startRowIdx + 1, columnCount: endColIdx - startColIdx + 1 };
}
function setAttrs(cell, attrs) {
    return util_assign(util_assign({}, cell.attrs), attrs);
}
function getCellSelectionClass(selection) {
    var proto = Object.getPrototypeOf(selection);
    return proto.constructor;
}
function createDummyCells(columnCount, rowIdx, schema, attrs) {
    if (attrs === void 0) { attrs = null; }
    var _a = schema.nodes, tableHeadCell = _a.tableHeadCell, tableBodyCell = _a.tableBodyCell, paragraph = _a.paragraph;
    var cell = rowIdx === 0 ? tableHeadCell : tableBodyCell;
    var cells = [];
    for (var index = 0; index < columnCount; index += 1) {
        cells.push(cell.create(attrs, paragraph.create()));
    }
    return cells;
}

;// CONCATENATED MODULE: ./src/wysiwyg/command/mergeCells.ts

function createMergeCellsCommand(context, OffsetMap) {
    var FragmentClass = context.pmModel.Fragment;
    var mergeCells = function (_, state, dispatch) {
        var selection = state.selection, tr = state.tr;
        var _a = getResolvedSelection(selection, context), anchor = _a.anchor, head = _a.head;
        // @ts-ignore
        // judge cell selection
        if (!anchor || !head || !selection.isCellSelection) {
            return false;
        }
        var map = OffsetMap.create(anchor);
        var CellSelection = getCellSelectionClass(selection);
        var totalRowCount = map.totalRowCount, totalColumnCount = map.totalColumnCount;
        var selectionInfo = map.getRectOffsets(anchor, head);
        var _b = getRowAndColumnCount(selectionInfo), rowCount = _b.rowCount, columnCount = _b.columnCount;
        var startRowIdx = selectionInfo.startRowIdx, startColIdx = selectionInfo.startColIdx, endRowIdx = selectionInfo.endRowIdx, endColIdx = selectionInfo.endColIdx;
        var allSelected = rowCount >= totalRowCount - 1 && columnCount === totalColumnCount;
        var hasTableHead = startRowIdx === 0 && endRowIdx > startRowIdx;
        if (allSelected || hasTableHead) {
            return false;
        }
        var fragment = FragmentClass.empty;
        for (var rowIdx = startRowIdx; rowIdx <= endRowIdx; rowIdx += 1) {
            for (var colIdx = startColIdx; colIdx <= endColIdx; colIdx += 1) {
                // set first cell content
                if (rowIdx === startRowIdx && colIdx === startColIdx) {
                    fragment = appendFragment(rowIdx, colIdx, fragment, map);
                    // set each cell content and delete the cell for spanning
                }
                else if (!map.extendedRowspan(rowIdx, colIdx) && !map.extendedColspan(rowIdx, colIdx)) {
                    var _c = map.getCellInfo(rowIdx, colIdx), offset = _c.offset, nodeSize = _c.nodeSize;
                    var from = tr.mapping.map(offset);
                    var to = from + nodeSize;
                    fragment = appendFragment(rowIdx, colIdx, fragment, map);
                    tr.delete(from, to);
                }
            }
        }
        var _d = map.getNodeAndPos(startRowIdx, startColIdx), node = _d.node, pos = _d.pos;
        // set rowspan, colspan to first root cell
        setSpanToRootCell(tr, fragment, {
            startNode: node,
            startPos: pos,
            rowCount: rowCount,
            columnCount: columnCount,
        });
        tr.setSelection(new CellSelection(tr.doc.resolve(pos)));
        dispatch(tr);
        return true;
    };
    return mergeCells;
}
function setSpanToRootCell(tr, fragment, rangeInfo) {
    var startNode = rangeInfo.startNode, startPos = rangeInfo.startPos, rowCount = rangeInfo.rowCount, columnCount = rangeInfo.columnCount;
    tr.setNodeMarkup(startPos, null, setAttrs(startNode, { colspan: columnCount, rowspan: rowCount }));
    if (fragment.size) {
        // add 1 for text start offset(not node start offset)
        tr.replaceWith(startPos + 1, startPos + startNode.content.size, fragment);
    }
}
function appendFragment(rowIdx, colIdx, fragment, map) {
    var targetFragment = map.getNodeAndPos(rowIdx, colIdx).node.content;
    // prevent to add empty string
    return targetFragment.size > 2 ? fragment.append(targetFragment) : fragment;
}

;// CONCATENATED MODULE: ./src/wysiwyg/command/splitCells.ts

function getColspanEndIdx(rowIdx, colIdx, map) {
    var endColIdx = colIdx;
    if (!map.extendedRowspan(rowIdx, colIdx) && map.extendedColspan(rowIdx, colIdx)) {
        var _a = map.getColspanStartInfo(rowIdx, colIdx), startSpanIdx = _a.startSpanIdx, count = _a.count;
        endColIdx = startSpanIdx + count;
    }
    return endColIdx;
}
function judgeInsertToNextRow(map, mappedPos, rowIdx, colIdx) {
    var totalColumnCount = map.totalColumnCount;
    return (map.extendedRowspan(rowIdx, colIdx) &&
        map.extendedRowspan(rowIdx, totalColumnCount - 1) &&
        mappedPos === map.posAt(rowIdx, totalColumnCount - 1));
}
function createSplitCellsCommand(context, OffsetMap) {
    var splitCells = function (_, state, dispatch, view) {
        var selection = state.selection, tr = state.tr;
        var _a = getResolvedSelection(selection, context), anchor = _a.anchor, head = _a.head;
        if (!anchor || !head) {
            return false;
        }
        var map = OffsetMap.create(anchor);
        var selectionInfo = map.getRectOffsets(anchor, head);
        var startRowIdx = selectionInfo.startRowIdx, startColIdx = selectionInfo.startColIdx, endRowIdx = selectionInfo.endRowIdx, endColIdx = selectionInfo.endColIdx;
        var lastCellPos = -1;
        for (var rowIdx = startRowIdx; rowIdx <= endRowIdx; rowIdx += 1) {
            for (var colIdx = startColIdx; colIdx <= endColIdx; colIdx += 1) {
                if (map.extendedRowspan(rowIdx, colIdx) || map.extendedColspan(rowIdx, colIdx)) {
                    // insert empty cell in spanning cell position
                    var node = map.getNodeAndPos(rowIdx, colIdx).node;
                    var colspanEndIdx = getColspanEndIdx(rowIdx, colIdx, map);
                    var mappedPos = map.posAt(rowIdx, colspanEndIdx);
                    var pos = tr.mapping.map(mappedPos);
                    // add 2(tr end, open tag length) to insert the cell on the next row
                    // in case that all next cells are spanning on the current row
                    if (judgeInsertToNextRow(map, mappedPos, rowIdx, colspanEndIdx)) {
                        pos += 2;
                    }
                    // get the last cell position for cell selection after splitting cells
                    lastCellPos = Math.max(pos, lastCellPos);
                    tr.insert(pos, node.type.createAndFill(setAttrs(node, { colspan: null, rowspan: null })));
                }
                else {
                    // remove colspan, rowspan of the root spanning cell
                    var _b = map.getNodeAndPos(rowIdx, colIdx), node = _b.node, pos = _b.pos;
                    // get the last cell position for cell selection after splitting cells
                    lastCellPos = Math.max(tr.mapping.map(pos), lastCellPos);
                    tr.setNodeMarkup(tr.mapping.map(pos), null, setAttrs(node, { colspan: null, rowspan: null }));
                }
            }
        }
        dispatch(tr);
        setCellSelection(view, selection, OffsetMap, map.tableStartOffset, selectionInfo);
        return true;
    };
    return splitCells;
}
function setCellSelection(view, selection, OffsetMap, tableStartPos, selectionInfo) {
    // @ts-ignore
    // judge cell selection
    if (selection.isCellSelection) {
        var tr = view.state.tr;
        var CellSelection = getCellSelectionClass(selection);
        var startRowIdx = selectionInfo.startRowIdx, startColIdx = selectionInfo.startColIdx, endRowIdx = selectionInfo.endRowIdx, endColIdx = selectionInfo.endColIdx;
        // get changed cell offsets
        var map = OffsetMap.create(tr.doc.resolve(tableStartPos));
        var startOffset = map.getCellInfo(startRowIdx, startColIdx).offset;
        var endOffset = map.getCellInfo(endRowIdx, endColIdx).offset;
        tr.setSelection(new CellSelection(tr.doc.resolve(startOffset), tr.doc.resolve(endOffset)));
        view.dispatch(tr);
    }
}

;// CONCATENATED MODULE: ./src/wysiwyg/command/removeColumn.ts

function createRemoveColumnCommand(context, OffsetMap) {
    var removeColumn = function (_, state, dispatch) {
        var selection = state.selection, tr = state.tr;
        var _a = getResolvedSelection(selection, context), anchor = _a.anchor, head = _a.head;
        if (!anchor || !head) {
            return false;
        }
        var map = OffsetMap.create(anchor);
        var selectionInfo = map.getRectOffsets(anchor, head);
        var totalColumnCount = map.totalColumnCount, totalRowCount = map.totalRowCount;
        var columnCount = getRowAndColumnCount(selectionInfo).columnCount;
        var selectedAllColumn = columnCount === totalColumnCount;
        if (selectedAllColumn) {
            return false;
        }
        var startColIdx = selectionInfo.startColIdx, endColIdx = selectionInfo.endColIdx;
        var mapStart = tr.mapping.maps.length;
        for (var rowIdx = 0; rowIdx < totalRowCount; rowIdx += 1) {
            for (var colIdx = endColIdx; colIdx >= startColIdx; colIdx -= 1) {
                var _b = map.getCellInfo(rowIdx, colIdx), offset = _b.offset, nodeSize = _b.nodeSize;
                var colspanInfo = map.getColspanStartInfo(rowIdx, colIdx);
                if (!map.extendedRowspan(rowIdx, colIdx)) {
                    // decrease colspan count inside the col-spanning cell
                    if ((colspanInfo === null || colspanInfo === void 0 ? void 0 : colspanInfo.count) > 1) {
                        var _c = map.getColspanStartInfo(rowIdx, colIdx), node = _c.node, pos = _c.pos;
                        var colspan = map.decreaseColspanCount(rowIdx, colIdx);
                        var attrs = setAttrs(node, { colspan: colspan > 1 ? colspan : null });
                        tr.setNodeMarkup(tr.mapping.slice(mapStart).map(pos), null, attrs);
                    }
                    else {
                        var from = tr.mapping.slice(mapStart).map(offset);
                        var to = from + nodeSize;
                        tr.delete(from, to);
                    }
                }
            }
        }
        dispatch(tr);
        return true;
    };
    return removeColumn;
}

;// CONCATENATED MODULE: ./src/wysiwyg/command/removeRow.ts

function getRowRanges(map, rowIdx) {
    var totalColumnCount = map.totalColumnCount;
    var from = Number.MAX_VALUE;
    var to = 0;
    for (var colIdx = 0; colIdx < totalColumnCount; colIdx += 1) {
        if (!map.extendedRowspan(rowIdx, colIdx)) {
            var _a = map.getCellInfo(rowIdx, colIdx), offset = _a.offset, nodeSize = _a.nodeSize;
            from = Math.min(from, offset);
            to = Math.max(to, offset + nodeSize);
        }
    }
    return { from: from, to: to };
}
function createRemoveRowCommand(context, OffsetMap) {
    var removeRow = function (_, state, dispatch) {
        var selection = state.selection, tr = state.tr;
        var _a = getResolvedSelection(selection, context), anchor = _a.anchor, head = _a.head;
        if (anchor && head) {
            var map = OffsetMap.create(anchor);
            var totalRowCount = map.totalRowCount, totalColumnCount = map.totalColumnCount;
            var selectionInfo = map.getRectOffsets(anchor, head);
            var rowCount = getRowAndColumnCount(selectionInfo).rowCount;
            var startRowIdx = selectionInfo.startRowIdx, endRowIdx = selectionInfo.endRowIdx;
            var selectedThead = startRowIdx === 0;
            var selectedAllTbodyRow = rowCount === totalRowCount - 1;
            if (selectedAllTbodyRow || selectedThead) {
                return false;
            }
            for (var rowIdx = endRowIdx; rowIdx >= startRowIdx; rowIdx -= 1) {
                var mapStart = tr.mapping.maps.length;
                var _b = getRowRanges(map, rowIdx), from = _b.from, to = _b.to;
                // delete table row
                tr.delete(from - 1, to + 1);
                for (var colIdx = 0; colIdx < totalColumnCount; colIdx += 1) {
                    var rowspanInfo = map.getRowspanStartInfo(rowIdx, colIdx);
                    if ((rowspanInfo === null || rowspanInfo === void 0 ? void 0 : rowspanInfo.count) > 1 && !map.extendedColspan(rowIdx, colIdx)) {
                        // decrease rowspan count inside the row-spanning cell
                        // eslint-disable-next-line max-depth
                        if (map.extendedRowspan(rowIdx, colIdx)) {
                            var _c = map.getRowspanStartInfo(rowIdx, colIdx), node = _c.node, pos = _c.pos;
                            var rowspan = map.decreaseRowspanCount(rowIdx, colIdx);
                            var attrs = setAttrs(node, { rowspan: rowspan > 1 ? rowspan : null });
                            tr.setNodeMarkup(tr.mapping.slice(mapStart).map(pos), null, attrs);
                            // the row-spanning cell should be moved down
                        }
                        else if (!map.extendedRowspan(rowIdx, colIdx)) {
                            var _d = map.getRowspanStartInfo(rowIdx, colIdx), node = _d.node, count = _d.count;
                            var attrs = setAttrs(node, { rowspan: count > 2 ? count - 1 : null });
                            var copiedCell = node.type.create(attrs, node.content);
                            tr.insert(tr.mapping.slice(mapStart).map(map.posAt(rowIdx + 1, colIdx)), copiedCell);
                        }
                    }
                }
                map = OffsetMap.create(tr.doc.resolve(map.tableStartOffset));
            }
            dispatch(tr);
            return true;
        }
        return false;
    };
    return removeRow;
}

;// CONCATENATED MODULE: ./src/wysiwyg/command/direction.ts
// eslint-disable-next-line no-shadow
var Direction;
(function (Direction) {
    Direction["LEFT"] = "left";
    Direction["RIGHT"] = "right";
    Direction["UP"] = "up";
    Direction["DOWN"] = "down";
})(Direction || (Direction = {}));

;// CONCATENATED MODULE: ./src/wysiwyg/command/addRow.ts


function getTargetRowInfo(direction, map, selectionInfo) {
    var targetRowIdx;
    var judgeToExtendRowspan;
    var insertColIdx;
    var nodeSize;
    if (direction === Direction.UP) {
        targetRowIdx = selectionInfo.startRowIdx;
        judgeToExtendRowspan = function (colIdx) { return map.extendedRowspan(targetRowIdx, colIdx); };
        insertColIdx = 0;
        nodeSize = -1;
    }
    else {
        targetRowIdx = selectionInfo.endRowIdx;
        judgeToExtendRowspan = function (colIdx) { return map.getRowspanCount(targetRowIdx, colIdx) > 1; };
        insertColIdx = map.totalColumnCount - 1;
        nodeSize = !map.extendedRowspan(targetRowIdx, insertColIdx)
            ? map.getCellInfo(targetRowIdx, insertColIdx).nodeSize + 1
            : 2;
    }
    return { targetRowIdx: targetRowIdx, judgeToExtendRowspan: judgeToExtendRowspan, insertColIdx: insertColIdx, nodeSize: nodeSize };
}
function createAddRowCommand(context, OffsetMap, direction) {
    var addRow = function (_, state, dispatch) {
        var selection = state.selection, schema = state.schema, tr = state.tr;
        var _a = getResolvedSelection(selection, context), anchor = _a.anchor, head = _a.head;
        if (!anchor || !head) {
            return false;
        }
        var map = OffsetMap.create(anchor);
        var totalColumnCount = map.totalColumnCount;
        var selectionInfo = map.getRectOffsets(anchor, head);
        var rowCount = getRowAndColumnCount(selectionInfo).rowCount;
        var _b = getTargetRowInfo(direction, map, selectionInfo), targetRowIdx = _b.targetRowIdx, judgeToExtendRowspan = _b.judgeToExtendRowspan, insertColIdx = _b.insertColIdx, nodeSize = _b.nodeSize;
        var selectedThead = targetRowIdx === 0;
        if (selectedThead) {
            return false;
        }
        var rows = [];
        var from = tr.mapping.map(map.posAt(targetRowIdx, insertColIdx)) + nodeSize;
        var cells = [];
        for (var colIdx = 0; colIdx < totalColumnCount; colIdx += 1) {
            // increase rowspan count inside the row-spanning cell
            if (judgeToExtendRowspan(colIdx)) {
                var _c = map.getRowspanStartInfo(targetRowIdx, colIdx), node = _c.node, pos = _c.pos;
                var attrs = setAttrs(node, { rowspan: node.attrs.rowspan + rowCount });
                tr.setNodeMarkup(tr.mapping.map(pos), null, attrs);
            }
            else {
                cells = cells.concat(createDummyCells(1, targetRowIdx, schema));
            }
        }
        for (var i = 0; i < rowCount; i += 1) {
            rows.push(schema.nodes.tableRow.create(null, cells));
        }
        dispatch(tr.insert(from, rows));
        return true;
    };
    return addRow;
}

;// CONCATENATED MODULE: ./src/wysiwyg/command/addColumn.ts


function getTargetColInfo(direction, map, selectionInfo) {
    var targetColIdx;
    var judgeToExtendColspan;
    var insertColIdx;
    if (direction === Direction.LEFT) {
        targetColIdx = selectionInfo.startColIdx;
        judgeToExtendColspan = function (rowIdx) { return map.extendedColspan(rowIdx, targetColIdx); };
        insertColIdx = targetColIdx;
    }
    else {
        targetColIdx = selectionInfo.endColIdx;
        judgeToExtendColspan = function (rowIdx) { return map.getColspanCount(rowIdx, targetColIdx) > 1; };
        insertColIdx = targetColIdx + 1;
    }
    return { targetColIdx: targetColIdx, judgeToExtendColspan: judgeToExtendColspan, insertColIdx: insertColIdx };
}
function createAddColumnCommand(context, OffsetMap, direction) {
    var addColumn = function (_, state, dispatch) {
        var selection = state.selection, tr = state.tr, schema = state.schema;
        var _a = getResolvedSelection(selection, context), anchor = _a.anchor, head = _a.head;
        if (!anchor || !head) {
            return false;
        }
        var map = OffsetMap.create(anchor);
        var selectionInfo = map.getRectOffsets(anchor, head);
        var _b = getTargetColInfo(direction, map, selectionInfo), targetColIdx = _b.targetColIdx, judgeToExtendColspan = _b.judgeToExtendColspan, insertColIdx = _b.insertColIdx;
        var columnCount = getRowAndColumnCount(selectionInfo).columnCount;
        var totalRowCount = map.totalRowCount;
        for (var rowIdx = 0; rowIdx < totalRowCount; rowIdx += 1) {
            // increase colspan count inside the col-spanning cell
            if (judgeToExtendColspan(rowIdx)) {
                var _c = map.getColspanStartInfo(rowIdx, targetColIdx), node = _c.node, pos = _c.pos;
                var attrs = setAttrs(node, { colspan: node.attrs.colspan + columnCount });
                tr.setNodeMarkup(tr.mapping.map(pos), null, attrs);
            }
            else {
                var cells = createDummyCells(columnCount, rowIdx, schema);
                tr.insert(tr.mapping.map(map.posAt(rowIdx, insertColIdx)), cells);
            }
        }
        dispatch(tr);
        return true;
    };
    return addColumn;
}

;// CONCATENATED MODULE: ./src/wysiwyg/commandFactory.ts







function createCommands(context, OffsetMap) {
    return {
        mergeCells: createMergeCellsCommand(context, OffsetMap),
        splitCells: createSplitCellsCommand(context, OffsetMap),
        addRowToUp: createAddRowCommand(context, OffsetMap, Direction.UP),
        addRowToDown: createAddRowCommand(context, OffsetMap, Direction.DOWN),
        removeRow: createRemoveRowCommand(context, OffsetMap),
        addColumnToLeft: createAddColumnCommand(context, OffsetMap, Direction.LEFT),
        addColumnToRight: createAddColumnCommand(context, OffsetMap, Direction.RIGHT),
        removeColumn: createRemoveColumnCommand(context, OffsetMap),
    };
}

;// CONCATENATED MODULE: ./src/index.ts








function tableMergedCellPlugin(context) {
    var i18n = context.i18n, eventEmitter = context.eventEmitter;
    var TableOffsetMap = eventEmitter.emitReduce('mixinTableOffsetMapPrototype', offsetMapMixin, createOffsetMapMixin);
    addLangs(i18n);
    addMergedTableContextMenu(context);
    return {
        toHTMLRenderers: toHTMLRenderers,
        markdownParsers: markdownParsers,
        toMarkdownRenderers: toMarkdownRenderers,
        wysiwygCommands: createCommands(context, TableOffsetMap),
    };
}

}();
__webpack_exports__ = __webpack_exports__["default"];
/******/ 	return __webpack_exports__;
/******/ })()
;
});