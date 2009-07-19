--TEST--
Test nowdoc and line numbering
--FILE--
<?php
function error_handler($num, $msg, $file, $line, $vars) {
	echo $line,"\n";
}
set_error_handler('error_handler');
trigger_error("line", E_USER_ERROR);
$x = <<<EOF
EOF;
var_dump($x);
trigger_error("line", E_USER_ERROR);
$x = <<<'EOF'
EOF;
var_dump($x);
trigger_error("line", E_USER_ERROR);
$x = <<<EOF
test
EOF;
var_dump($x);
trigger_error("line", E_USER_ERROR);
$x = <<<'EOF'
test
EOF;
var_dump($x);
trigger_error("line", E_USER_ERROR);
$x = <<<EOF
test1
test2

test3


EOF;
var_dump($x);
trigger_error("line", E_USER_ERROR);
$x = <<<'EOF'
test1
test2

test3


EOF;
var_dump($x);
trigger_error("line", E_USER_ERROR);
echo "ok\n";
?>
--EXPECT--
6
unicode(0) ""
10
unicode(0) ""
14
unicode(4) "test"
19
unicode(4) "test"
24
unicode(20) "test1
test2

test3

"
34
unicode(20) "test1
test2

test3

"
44
ok
