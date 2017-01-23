/**
 *
 */
function insertShortcode(id_name, str) {

  // 挿入する文字列
  var strInsert = str;

  // 現在のテキストエリアの文字列
  var strOriginal = document.getElementById(id_name).value;

  // 現在のカーソル位置
  var posCursole = document.getElementById(id_name).selectionStart;

  // カーソル位置より左の文字列
  var leftPart = strOriginal.substr(0, posCursole);

  // カーソル位置より右の文字列
  var rightPart = strOriginal.substr(posCursole, strOriginal.length);

  // 文字列を結合して、テキストエリアに出力
  document.getElementById(id_name).value = leftPart + strInsert + rightPart;
}