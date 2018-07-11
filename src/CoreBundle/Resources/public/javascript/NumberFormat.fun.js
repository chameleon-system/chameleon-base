function NumberFormat(number, decimals, dec_point, thousands_sep) {
  var tmpNumber = number;
  var output = '';
  if (tmpNumber == 0) {
    output = '0';
	if (decimals >0) output = output + dec_point;
    for(var i =0; i < decimals; i++) output = output + '0';
  } else {
    var sign = false;
    if (tmpNumber < 0) {
      sign = true;
      tmpNumber = tmpNumber * -1;
    }
    var tnum = (Math.round(tmpNumber * Math.pow(10,decimals))).toString();
    var pos = 0;
    var gotDec = false;
    if (decimals == 0) {
      gotDec = true;
      pos = -1;
    }
    for(var i = tnum.length; i >0; i--) {
      pos++;
      if (gotDec && (pos>0) && ((pos % 3)==0)) output = thousands_sep + output;
      output = tnum.charAt(i-1) + output;
      if (!gotDec &&(pos == decimals)) {
        if (decimals != 0) output = dec_point + output;
        gotDec = true;
        pos = -1;
      }
    }
    // do we need some leading zeros?
    if (decimals >0 && !gotDec) {
      for(var i = output.length; i < decimals;i++) output = '0' + output;
      output = '0' + dec_point + output;
    }
    if (output.charAt(0) == dec_point) output = '0' + output;
    if (sign) output = '-' + output;
  }
  return output;
}

function NumberToFloat(number, dec_point, thousands_sep) {
  var output = '';
  var tnum = number.toString();
  for(var i=0;i<tnum.length;i++) {
    if (tnum.charAt(i) != dec_point && tnum.charAt(i) != thousands_sep) output = output + tnum.charAt(i);
    else if (tnum.charAt(i) == dec_point) {
      output = output + '.';
    }
  }
  return output;
}