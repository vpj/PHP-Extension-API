--TEST--
jdtojewish() function
--SKIPIF--
<?php include 'skipif.inc'; ?>
--INI--
unicode.output_encoding=ISO-8859-8
--FILE--
<?php

var_dump(jdtojewish(gregoriantojd(10,28,2002))."\r\n".
	jdtojewish(gregoriantojd(10,28,2002),true)."\r\n".
	jdtojewish(gregoriantojd(10,28,2002),true, CAL_JEWISH_ADD_ALAFIM_GERESH)."\r\n".
	jdtojewish(gregoriantojd(10,28,2002),true, CAL_JEWISH_ADD_ALAFIM)."\r\n".
	jdtojewish(gregoriantojd(10,28,2002),true, CAL_JEWISH_ADD_ALAFIM_GERESH+CAL_JEWISH_ADD_ALAFIM)."\r\n".
	jdtojewish(gregoriantojd(10,28,2002),true, CAL_JEWISH_ADD_GERESHAYIM)."\r\n".
	jdtojewish(gregoriantojd(10,8,2002),true, CAL_JEWISH_ADD_GERESHAYIM)."\r\n".
	jdtojewish(gregoriantojd(10,8,2002),true, CAL_JEWISH_ADD_GERESHAYIM+CAL_JEWISH_ADD_ALAFIM_GERESH)."\r\n".
	jdtojewish(gregoriantojd(10,8,2002),true, CAL_JEWISH_ADD_GERESHAYIM+CAL_JEWISH_ADD_ALAFIM)."\r\n".
	jdtojewish(gregoriantojd(10,8,2002),true, CAL_JEWISH_ADD_GERESHAYIM+CAL_JEWISH_ADD_ALAFIM+CAL_JEWISH_ADD_ALAFIM_GERESH)."\r\n");
?>
--EXPECT--
unicode(184) "2/22/5763
�� ���� �����
�� ���� �'����
�� ���� � ����� ����
�� ���� �' ����� ����
�"� ���� ����"�
�' ���� ����"�
�' ���� �'���"�
�' ���� � ����� ���"�
�' ���� �' ����� ���"�
"
