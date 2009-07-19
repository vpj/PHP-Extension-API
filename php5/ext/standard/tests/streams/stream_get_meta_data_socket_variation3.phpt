--TEST--
Testing stream_get_meta_data() "blocked" field on a udp socket
--FILE--
<?php

/* Setup socket server */
$server = stream_socket_server('tcp://127.0.0.1:31337');

/* Connect to it */
$client = fsockopen('tcp://127.0.0.1:31337');
if (!$client) {
	die("Unable to create socket");
}

/* Accept that connection */
$socket = stream_socket_accept($server);

var_dump(stream_get_meta_data($client));

echo "\n\nSet blocking to false:\n";
var_dump(socket_set_blocking($client, 0));
var_dump(stream_get_meta_data($client));

echo "\n\nSet blocking to true:\n";
var_dump(socket_set_blocking($client, 1));
var_dump(stream_get_meta_data($client));

fclose($client);
fclose($socket);
fclose($server);

?>
--EXPECTF--
array(8) {
  [u"stream_type"]=>
  unicode(%d) "tcp_socke%s"
  [u"mode"]=>
  unicode(2) "r+"
  [u"unread_bytes"]=>
  int(0)
  [u"unread_chars"]=>
  int(0)
  [u"seekable"]=>
  bool(false)
  [u"timed_out"]=>
  bool(false)
  [u"blocked"]=>
  bool(true)
  [u"eof"]=>
  bool(false)
}


Set blocking to false:
bool(true)
array(8) {
  [u"stream_type"]=>
  unicode(%d) "tcp_socke%s"
  [u"mode"]=>
  unicode(2) "r+"
  [u"unread_bytes"]=>
  int(0)
  [u"unread_chars"]=>
  int(0)
  [u"seekable"]=>
  bool(false)
  [u"timed_out"]=>
  bool(false)
  [u"blocked"]=>
  bool(false)
  [u"eof"]=>
  bool(false)
}


Set blocking to true:
bool(true)
array(8) {
  [u"stream_type"]=>
  unicode(%d) "tcp_socke%s"
  [u"mode"]=>
  unicode(2) "r+"
  [u"unread_bytes"]=>
  int(0)
  [u"unread_chars"]=>
  int(0)
  [u"seekable"]=>
  bool(false)
  [u"timed_out"]=>
  bool(false)
  [u"blocked"]=>
  bool(true)
  [u"eof"]=>
  bool(false)
}
