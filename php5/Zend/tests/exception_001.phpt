--TEST--
Testing nested exceptions
--FILE--
<?php

try {
	try {
		try {
			try {
				throw new Exception(NULL);
			} catch (Exception $e) {
				var_dump($e->getMessage());
				throw $e;
			}
		} catch (Exception $e) {
			var_dump($e->getMessage());
			throw $e;
		}
	} catch (Exception $e) {
		var_dump($e->getMessage());
		throw $e;
	}
} catch (Exception $e) {
	var_dump($e->getMessage());
	throw $e;
}

?>
--EXPECTF--
unicode(0) ""
unicode(0) ""
unicode(0) ""
unicode(0) ""

Fatal error: Uncaught exception 'Exception' in %s:%d
Stack trace:
#0 {main}
  thrown in %s on line %d
