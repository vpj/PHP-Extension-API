--TEST--
Testing $argc and $argv handling (GET)
--INI--
register_argc_argv=1
--GET--
ab+cd+ef+123+test
--FILE--
<?php 
for ($i=0; $i<$_SERVER['argc']; $i++) {
	echo "$i: ".$_SERVER['argv'][$i]."\n";
}
?>
--EXPECT--
0: ab
1: cd
2: ef
3: 123
4: test
