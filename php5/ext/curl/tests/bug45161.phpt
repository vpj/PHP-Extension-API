--TEST--
Bug #45161 (Reusing a curl handle leaks memory)
--SKIPIF--
<?php $curl_version = curl_version(); if ($curl_version['version_number'] < 0x071100) die("skip: test works only with curl >= 7.17.0"); ?>
--FILE--
<?php

// Fill memory for test
$ch = curl_init();
$fp = fopen('/dev/null', 'w');

/*
$i = $start = $end = 100000.00;
for ($i = 0; $i < 100; $i++) {
	curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:9/');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);
}
*/

// Start actual test
$start = memory_get_usage() + 1024;
for($i = 0; $i < 1024; $i++) {
	curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:9/');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);
}
if ($start < memory_get_usage()) {
	echo 'FAIL';
} else {
	echo 'PASS';
}
echo "\n";
fclose($fp);
unset($fp);
?>
--EXPECT--
PASS
