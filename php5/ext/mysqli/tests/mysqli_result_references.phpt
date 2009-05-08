--TEST--
References to result sets
--SKIPIF--
<?php 
require_once('skipif.inc');
require_once('skipifemb.inc'); 
require_once('skipifconnectfailure.inc');
?>
--FILE--
<?php
	require_once('connect.inc');
	require_once('table.inc');

	$references = array();

	if (!(mysqli_real_query($link, "SELECT id, label FROM test ORDER BY id ASC LIMIT 2")) ||
			!($res = mysqli_store_result($link)))
		printf("[001] [%d] %s\n", mysqli_errno($link), mysqli_error($link));

	$idx = 0;
	while ($row = mysqli_fetch_assoc($res)) {
		/* mysqlnd: force seperation - create copies */
		$references[$idx] = array(
			'id' 		=> &$row['id'],
			'label'	=> $row['label'] . '');
		$references[$idx++]['id'] += 0;
	}

	mysqli_close($link);

	mysqli_data_seek($res, 0);
	while ($row = mysqli_fetch_assoc($res)) {
		/* mysqlnd: force seperation - create copies */
		$references[$idx] = array(
			'id' 		=> &$row['id'],
			'label'	=> $row['label'] . '');
		$references[$idx++]['id'] += 0;
	}

	mysqli_free_result($res);

	if (!$link = mysqli_connect($host, $user, $passwd, $db, $port, $socket))
		printf("[002] Cannot connect to the server using host=%s, user=%s, passwd=***, dbname=%s, port=%s, socket=%s\n",
			$host, $user, $db, $port, $socket);

	if (!(mysqli_real_query($link, "SELECT id, label FROM test ORDER BY id ASC LIMIT 2")) ||
			!($res = mysqli_use_result($link)))
		printf("[003] [%d] %s\n", mysqli_errno($link), mysqli_error($link));

	while ($row = mysqli_fetch_assoc($res)) {
		/* mysqlnd: force seperation - create copies*/
		$references[$idx] = array(
			'id' 		=> &$row['id'],
			'label'	=> $row['label'] . '');
		$references[$idx]['id2'] = &$references[$idx]['id'];
		$references[$idx]['id'] += 1;
		$references[$idx++]['id2'] += 1;
	}

	$references[$idx++] = &$res;
	mysqli_free_result($res);
	@debug_zval_dump($references);

	if (!(mysqli_real_query($link, "SELECT id, label FROM test ORDER BY id ASC LIMIT 1")) ||
			!($res = mysqli_use_result($link)))
		printf("[004] [%d] %s\n", mysqli_errno($link), mysqli_error($link));

	$tmp = array();
	while ($row = mysqli_fetch_assoc($res)) {
		$tmp[] = $row;
	}
	$tmp = unserialize(serialize($tmp));
	debug_zval_dump($tmp);
	mysqli_free_result($res);

	mysqli_close($link);
	print "done!";
?>
--EXPECTF--
array(7) refcount(2){
  [0]=>
  array(2) refcount(1){
    ["id"]=>
    long(1) refcount(1)
    ["label"]=>
    string(1) "a" refcount(1)
  }
  [1]=>
  array(2) refcount(1){
    ["id"]=>
    long(2) refcount(1)
    ["label"]=>
    string(1) "b" refcount(1)
  }
  [2]=>
  array(2) refcount(1){
    ["id"]=>
    long(1) refcount(1)
    ["label"]=>
    string(1) "a" refcount(1)
  }
  [3]=>
  array(2) refcount(1){
    ["id"]=>
    long(2) refcount(1)
    ["label"]=>
    string(1) "b" refcount(1)
  }
  [4]=>
  array(3) refcount(1){
    ["id"]=>
    &long(3) refcount(2)
    ["label"]=>
    string(1) "a" refcount(1)
    ["id2"]=>
    &long(3) refcount(2)
  }
  [5]=>
  array(3) refcount(1){
    ["id"]=>
    &long(4) refcount(2)
    ["label"]=>
    string(1) "b" refcount(1)
    ["id2"]=>
    &long(4) refcount(2)
  }
  [6]=>
  &object(mysqli_result)#2 (5) refcount(2){
    ["current_field"]=>
    NULL refcount(1)
    ["field_count"]=>
    NULL refcount(1)
    ["lengths"]=>
    NULL refcount(1)
    ["num_rows"]=>
    NULL refcount(1)
    ["type"]=>
    NULL refcount(1)
  }
}
array(1) refcount(2){
  [0]=>
  array(2) refcount(1){
    ["id"]=>
    string(1) "1" refcount(1)
    ["label"]=>
    string(1) "a" refcount(1)
  }
}
done!
--UEXPECTF--
array(7) refcount(2){
  [0]=>
  array(2) refcount(1){
    [u"id" { 0069 0064 }]=>
    long(1) refcount(1)
    [u"label" { 006c 0061 0062 0065 006c }]=>
    unicode(1) "a" { 0061 } refcount(1)
  }
  [1]=>
  array(2) refcount(1){
    [u"id" { 0069 0064 }]=>
    long(2) refcount(1)
    [u"label" { 006c 0061 0062 0065 006c }]=>
    unicode(1) "b" { 0062 } refcount(1)
  }
  [2]=>
  array(2) refcount(1){
    [u"id" { 0069 0064 }]=>
    long(1) refcount(1)
    [u"label" { 006c 0061 0062 0065 006c }]=>
    unicode(1) "a" { 0061 } refcount(1)
  }
  [3]=>
  array(2) refcount(1){
    [u"id" { 0069 0064 }]=>
    long(2) refcount(1)
    [u"label" { 006c 0061 0062 0065 006c }]=>
    unicode(1) "b" { 0062 } refcount(1)
  }
  [4]=>
  array(3) refcount(1){
    [u"id" { 0069 0064 }]=>
    &long(3) refcount(2)
    [u"label" { 006c 0061 0062 0065 006c }]=>
    unicode(1) "a" { 0061 } refcount(1)
    [u"id2" { 0069 0064 0032 }]=>
    &long(3) refcount(2)
  }
  [5]=>
  array(3) refcount(1){
    [u"id" { 0069 0064 }]=>
    &long(4) refcount(2)
    [u"label" { 006c 0061 0062 0065 006c }]=>
    unicode(1) "b" { 0062 } refcount(1)
    [u"id2" { 0069 0064 0032 }]=>
    &long(4) refcount(2)
  }
  [6]=>
  &object(mysqli_result)#2 (0) refcount(2){
  }
}
array(1) refcount(2){
  [0]=>
  array(2) refcount(1){
    [u"id" { 0069 0064 }]=>
    unicode(1) "1" { 0031 } refcount(1)
    [u"label" { 006c 0061 0062 0065 006c }]=>
    unicode(1) "a" { 0061 } refcount(1)
  }
}
done!
