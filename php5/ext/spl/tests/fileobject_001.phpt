--TEST--
SPL: SplFileObject::seek'ing
--FILE--
<?php

$o = new SplFileObject(dirname(__FILE__) . '/fileobject_001a.txt');

var_dump($o->key());
var_dump($o->current());
$o->setFlags(SplFileObject::DROP_NEW_LINE);
var_dump($o->key());
var_dump($o->current());
var_dump($o->key());
$o->next();
var_dump($o->key());
var_dump($o->current());
var_dump($o->key());
$o->rewind();
var_dump($o->key());
var_dump($o->current());
var_dump($o->key());
$o->seek(4);
var_dump($o->key());
var_dump($o->current());
var_dump($o->key());

echo "===A===\n";
foreach($o as $n => $l)
{
	var_dump($n, $l);
}

echo "===B===\n";
$o = new SplFileObject(dirname(__FILE__) . '/fileobject_001b.txt');
$o->setFlags(SplFileObject::DROP_NEW_LINE);
foreach($o as $n => $l)
{
	var_dump($n, $l);
}

?>
===DONE===
--EXPECT--
int(0)
unicode(2) "0
"
int(0)
unicode(2) "0
"
int(0)
int(1)
unicode(1) "1"
int(1)
int(0)
unicode(1) "0"
int(0)
int(4)
unicode(1) "4"
int(4)
===A===
int(0)
unicode(1) "0"
int(1)
unicode(1) "1"
int(2)
unicode(1) "2"
int(3)
unicode(1) "3"
int(4)
unicode(1) "4"
int(5)
unicode(1) "5"
int(6)
unicode(0) ""
===B===
int(0)
unicode(1) "0"
int(1)
unicode(1) "1"
int(2)
unicode(1) "2"
int(3)
unicode(1) "3"
int(4)
unicode(1) "4"
int(5)
unicode(1) "5"
===DONE===
