--TEST--
timezone_offset_get() tests
--INI--
date.timezone=UTC
--FILE--
<?php

$dto = date_create("2006-12-12");
var_dump($dto);

$dtz = date_timezone_get($dto);
var_dump($dtz);

var_dump(timezone_offset_get());
var_dump(timezone_offset_get($dtz, $dto));
var_dump(timezone_offset_get($dto, $dtz));

echo "Done\n";

?>
--EXPECTF--
object(DateTime)#%d (3) {
  [u"date"]=>
  unicode(19) "2006-12-12 00:00:00"
  [u"timezone_type"]=>
  int(3)
  [u"timezone"]=>
  unicode(3) "UTC"
}
object(DateTimeZone)#%d (0) {
}

Warning: timezone_offset_get() expects exactly 2 parameters, 0 given in %s on line %d
bool(false)
int(0)

Warning: timezone_offset_get() expects parameter 1 to be DateTimeZone, object given in %s on line %d
bool(false)
Done
