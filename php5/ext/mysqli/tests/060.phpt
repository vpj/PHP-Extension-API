--TEST--
mysqli_fetch_object with classes
--SKIPIF--
<?php 
require_once('skipif.inc'); 
require_once('skipifconnectfailure.inc');
?>
--FILE--
<?php
	include "connect.inc";

	class test_class {
		function __construct($arg1, $arg2) {
			echo __METHOD__ . "($arg1,$arg2)\n";
		}
	}

	/*** test mysqli_connect 127.0.0.1 ***/
	$link = mysqli_connect($host, $user, $passwd, $db, $port, $socket);

	mysqli_select_db($link, $db);
	mysqli_query($link, "SET sql_mode=''");

	mysqli_query($link,"DROP TABLE IF EXISTS test_fetch");
	mysqli_query($link,"CREATE TABLE test_fetch(c1 smallint unsigned,
		c2 smallint unsigned,
		c3 smallint,
		c4 smallint,
		c5 smallint,
		c6 smallint unsigned,
		c7 smallint)");

	mysqli_query($link, "INSERT INTO test_fetch VALUES ( -23, 35999, NULL, -500, -9999999, -0, 0)");

	$result = mysqli_query($link, "SELECT * FROM test_fetch");
	$test = mysqli_fetch_object($result, 'test_class', array(1, 2));
	mysqli_free_result($result);

	var_dump($test);

	mysqli_close($link);

	echo "Done\n";
?>
--EXPECTF--
test_class::__construct(1,2)
object(test_class)#%d (7) {
  ["c1"]=>
  string(1) "0"
  ["c2"]=>
  string(5) "35999"
  ["c3"]=>
  NULL
  ["c4"]=>
  string(4) "-500"
  ["c5"]=>
  string(6) "-32768"
  ["c6"]=>
  string(1) "0"
  ["c7"]=>
  string(1) "0"
}
Done
--UEXPECTF--
test_class::__construct(1,2)
object(test_class)#%d (7) {
  [u"c1"]=>
  unicode(1) "0"
  [u"c2"]=>
  unicode(5) "35999"
  [u"c3"]=>
  NULL
  [u"c4"]=>
  unicode(4) "-500"
  [u"c5"]=>
  unicode(6) "-32768"
  [u"c6"]=>
  unicode(1) "0"
  [u"c7"]=>
  unicode(1) "0"
}
Done
