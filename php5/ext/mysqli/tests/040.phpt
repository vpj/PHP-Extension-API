--TEST--
function test: mysqli_num_rows()
--SKIPIF--
<?php
require_once('skipif.inc');
require_once('skipifconnectfailure.inc');
?>
--FILE--
<?php
	include "connect.inc";

	/*** test mysqli_connect 127.0.0.1 ***/
	$link = mysqli_connect($host, $user, $passwd, $db, $port, $socket);

	mysqli_select_db($link, $db);

	mysqli_query($link, "DROP TABLE IF EXISTS test_result");
	mysqli_query($link, "CREATE TABLE test_result (a int, b varchar(10)) ENGINE=" . $engine);
	mysqli_query($link, "INSERT INTO test_result VALUES (1, 'foo')");

	mysqli_real_query($link, "SELECT * FROM test_result");
	if (mysqli_field_count($link)) {
		$result = mysqli_store_result($link);
		$num = mysqli_num_rows($result);
		mysqli_free_result($result);
	}

	var_dump($num);

	mysqli_query($link, "DROP TABLE IF EXISTS test_result");
	mysqli_close($link);
	print "done!";
?>
--EXPECT--
int(1)
done!