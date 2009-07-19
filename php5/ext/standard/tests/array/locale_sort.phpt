--TEST--
Sort with SORT_LOCALE_STRING
--SKIPIF--
<?php
if (substr(PHP_OS, 0, 3) == 'WIN') {
  die("skip Unix locale name only, not available on windows (and crashes with VC6)\n");
}
if (false == setlocale(LC_CTYPE, "fr_FR", "fr_FR.ISO8859-1")) {
  die("skip setlocale() failed\n");
}
?>
--INI--
unicode.script_encoding=ISO8859-1
unicode.output_encoding=ISO8859-1
--FILE--
<?php
setlocale(LC_ALL, 'fr_FR', 'fr_FR.ISO8859-1');
$table = array("AB" => "Alberta",
"BC" => "Colombie-Britannique",
"MB" => "Manitoba",
"NB" => "Nouveau-Brunswick",
"NL" => "Terre-Neuve-et-Labrador",
"NS" => "Nouvelle-�cosse",
"ON" => "Ontario",
"PE" => "�le-du-Prince-�douard",
"QC" => "Qu�bec",
"SK" => "Saskatchewan",
"NT" => "Territoires du Nord-Ouest",
"NU" => "Nunavut",
"YT" => "Territoire du Yukon");
asort($table, SORT_LOCALE_STRING);
var_dump($table);
?>
--EXPECTF--
Deprecated: setlocale(): deprecated in Unicode mode, please use ICU locale functions in %s on line %d
array(13) {
  [u"AB"]=>
  unicode(7) "Alberta"
  [u"BC"]=>
  unicode(20) "Colombie-Britannique"
  [u"PE"]=>
  unicode(21) "�le-du-Prince-�douard"
  [u"MB"]=>
  unicode(8) "Manitoba"
  [u"NB"]=>
  unicode(17) "Nouveau-Brunswick"
  [u"NS"]=>
  unicode(15) "Nouvelle-�cosse"
  [u"NU"]=>
  unicode(7) "Nunavut"
  [u"ON"]=>
  unicode(7) "Ontario"
  [u"QC"]=>
  unicode(6) "Qu�bec"
  [u"SK"]=>
  unicode(12) "Saskatchewan"
  [u"NL"]=>
  unicode(23) "Terre-Neuve-et-Labrador"
  [u"YT"]=>
  unicode(19) "Territoire du Yukon"
  [u"NT"]=>
  unicode(25) "Territoires du Nord-Ouest"
}
