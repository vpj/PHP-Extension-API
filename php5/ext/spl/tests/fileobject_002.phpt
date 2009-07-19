--TEST--
SPL: SplFileObject::fgetc
--FILE--
<?php

function test($name)
{
	echo "===$name===\n";

	$o = new SplFileObject(dirname(__FILE__) . '/' . $name);

	var_dump($o->key());
	while(($c = $o->fgetc()) !== false)
	{
		var_dump($o->key(), $c, $o->eof());
	}
	echo "===EOF?===\n";
	var_dump($o->eof());
	var_dump($o->key());
	var_dump($o->eof());
}

test('fileobject_001a.txt');
test('fileobject_001b.txt');

?>
===DONE===
<?php exit(0); ?>
--EXPECT--
===fileobject_001a.txt===
int(0)
int(0)
unicode(1) "0"
bool(false)
int(1)
unicode(1) "
"
bool(false)
int(1)
unicode(1) "1"
bool(false)
int(2)
unicode(1) "
"
bool(false)
int(2)
unicode(1) "2"
bool(false)
int(3)
unicode(1) "
"
bool(false)
int(3)
unicode(1) "3"
bool(false)
int(4)
unicode(1) "
"
bool(false)
int(4)
unicode(1) "4"
bool(false)
int(5)
unicode(1) "
"
bool(false)
int(5)
unicode(1) "5"
bool(false)
int(6)
unicode(1) "
"
bool(false)
===EOF?===
bool(true)
int(6)
bool(true)
===fileobject_001b.txt===
int(0)
int(0)
unicode(1) "0"
bool(false)
int(1)
unicode(1) "
"
bool(false)
int(1)
unicode(1) "1"
bool(false)
int(2)
unicode(1) "
"
bool(false)
int(2)
unicode(1) "2"
bool(false)
int(3)
unicode(1) "
"
bool(false)
int(3)
unicode(1) "3"
bool(false)
int(4)
unicode(1) "
"
bool(false)
int(4)
unicode(1) "4"
bool(false)
int(5)
unicode(1) "
"
bool(false)
int(5)
unicode(1) "5"
bool(false)
===EOF?===
bool(true)
int(5)
bool(true)
===DONE===
