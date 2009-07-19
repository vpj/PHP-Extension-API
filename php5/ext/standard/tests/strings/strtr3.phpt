--TEST--
strtr() function (windown-1251 encoding)
--INI--
unicode.script_encoding=windows-1251
unicode.output_encoding=windows-1251
--FILE--
<?php
$trans = array("�஢�"=>"�ࠢ�", "�ࠢ�"=>"�஢�", "�"=>"�", "��� ����"=>"�� �ࠢ�");
var_dump(strtr("# �� ���� �஢�, ��� ���� �ࠢ�. #", $trans));
?>
--EXPECT--
unicode(35) "# �� ���� �ࠢ�, �� �ࠢ� �஢�. #"
