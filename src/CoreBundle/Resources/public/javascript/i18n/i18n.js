/*
  * Translation object for javascript messages
  * 
  * For each language needed:
  * CHAMELEON.CORE.i18n.Translation holds an array of all translations
  * CHAMELEON.CORE.i18n.Date holds month and day names
  * CHAMELEON.CORE.i18n.GetLocalDateString(dateobject) returns the date in local format for a javascript Date-object
  * 
  * Usage:
  * Use the function CHAMELEON.CORE.i18n.Translate(string, replaceVar1, replaceVar2, ...) to translate your javascript output whereas string is the text to translate.
  * Optionally you can pass any amount of variables you want to have inserted into the string (replaceVar1 - replaceVarN). 
  * We need a corresponding placeholder in the string for each variable in the form % + type + number
  * type can be 's' for string, 'i' for integer or 'd' for date (you have to pass Y-m-d H:i:s as replaceVar)
  * the number is the position of the replaceVar in the argument list
  * 
  * CHAMELEON.CORE.i18n.Translate('String to translate %s1 - %s2 - %d3','inserted-string-1', 'inserted-string-2','2010-07-16 11:16:22');
  * => in base language: String to translate inserted-string-1 - inserted-string-2 - 16.07.2010 11:16
  * => with CHAMELEON.CORE.i18n.Translation = {'String to translate %s1 - %s2 - %d3':'%d3: Translated String %s2 - %s1'};
  *    16.07.2010 11:16: String to translate inserted-string-2 - inserted-string-1 
  *
  *
  * Use CHAMELEON.CORE.i18n.GetDayName() and CHAMELEON.CORE.i18n.GetMonthName() to get localized day- and month names
*/

if ( typeof CHAMELEON === "undefined" || !CHAMELEON ) { var CHAMELEON = {}; }
CHAMELEON.CORE = CHAMELEON.CORE || {};


CHAMELEON.CORE.i18n = CHAMELEON.CORE.i18n || {};

CHAMELEON.CORE.i18n.Translate = function (string) {
  string = this.GetTranslation(string);
  var bits = string.split('%');
  var output = bits[0];
  var regex = /^([ids])(\d)(.*)$/;
  for (var i=1; i<bits.length; i++) {
    parts = regex.exec(bits[i]);
    if (!parts || arguments[i]==null) continue;
    if (parts[1] == 'i') {
      output += parseInt(arguments[parts[2]], 10);
    } else if (parts[1] == 's') {
      output += arguments[parts[2]];
    } else if (parts[1] == 'd') {
      output += this.GetLocalDateFromDateTime(arguments[parts[2]]);
    }
    output += parts[3];
  }
  return output;
};

CHAMELEON.CORE.i18n.GetLocalDateFromDateTime = function (datetime) {
    var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
    var parts=datetime.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
    var JSDate = new Date(parts[0],parts[1]-1,parts[2],parts[3],parts[4],parts[5]);
    return this.GetLocalDateString(JSDate);
};

CHAMELEON.CORE.i18n.GetTranslation = function (string) {
    if (CHAMELEON.CORE.i18n.isInitialized === false) {
        console.log('unable to translate ' + string);
        return string;
    }
  if (typeof(CHAMELEON.CORE.i18n.Translation)!='undefined' && CHAMELEON.CORE.i18n.Translation[string]) {
    return CHAMELEON.CORE.i18n.Translation[string];
  }
  return string;
};

/*
  * dayNumber 0 = Sunday, 6 = Saturday
  * Type = full, medium, short
*/
CHAMELEON.CORE.i18n.GetDayName = function (dayNumber, Type) {
  if(typeof(Type)=='undefined' || Type == 'full') usedObject = CHAMELEON.CORE.i18n.Date.dayNames;
  if(Type == 'medium') usedObject = CHAMELEON.CORE.i18n.Date.dayNamesMedium;
  if(Type == 'short') usedObject = CHAMELEON.CORE.i18n.Date.dayNamesShort;
  if (typeof(usedObject)!='undefined' && usedObject) {
    if(typeof(usedObject[dayNumber])!='undefined' && usedObject[dayNumber]) {
      return usedObject[dayNumber];
    } else return '';
  } else return '';
};

/*
  * monthNumber 1 = January, 12 = December
  * Type = full, short
*/
CHAMELEON.CORE.i18n.GetMonthName = function (monthNumber, Type) {
  monthNumber = monthNumber-1;
  if(typeof(Type)=='undefined' || Type == 'full') usedObject = CHAMELEON.CORE.i18n.Date.monthNames;
  if(Type == 'short') usedObject = CHAMELEON.CORE.i18n.Date.monthNamesShort;
  if (typeof(usedObject)!='undefined' && usedObject) {
    if(typeof(usedObject[monthNumber])!='undefined' && usedObject[monthNumber]) {
      return usedObject[monthNumber];
    } else return '';
  } else return '';
};

CHAMELEON.CORE.i18n.Date = {
    monthNames: [
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.january'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.february'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.march'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.april'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.may'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.june'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.july'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.august'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.september'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.october'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.november'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month.december')
    ],
    monthNamesShort: [
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.january'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.february'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.march'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.april'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.may'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.june'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.july'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.august'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.september'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.october'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.november'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.month_short.december')
    ],
    dayNames: [
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day.sunday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day.monday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day.tuesday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day.wednesday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day.thursday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day.friday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day.saturday')
    ],
    dayNamesMedium: [
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_medium.sunday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_medium.monday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_medium.tuesday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_medium.wednesday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_medium.thursday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_medium.friday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_medium.saturday')
    ],
    dayNamesShort: [
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_short.sunday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_short.monday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_short.tuesday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_short.wednesday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_short.thursday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_short.friday'),
        CHAMELEON.CORE.i18n.Translate('chameleon_system_core.day_short.saturday')
    ]
};