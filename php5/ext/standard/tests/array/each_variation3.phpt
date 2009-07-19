--TEST--
Test each() function : usage variations - keys of different data types
--FILE--
<?php
/* Prototype  : array each(array $arr)
 * Description: Return the currently pointed key..value pair in the passed array,
 * and advance the pointer to the next element 
 * Source code: Zend/zend_builtin_functions.c
 */

/*
 * Pass each() arrays where the keys are different data types to test behaviour
 */

echo "*** Testing each() : usage variations ***\n";

//get an unset variable
$unset_var = 10;
unset ($unset_var);

// heredoc string
$heredoc = <<<EOT
hello world
EOT;

// unexpected values to be passed as $arr
$inputs = array(

       // int data
/*1*/  'int' => array(
       0 => 'zero',
       1 => 'one',
       12345 => 'positive',
       -2345 => 'negative',
       ),

       // float data
/*2*/  'float' => array(
       10.5 => 'positive',
       -10.5 => 'negative',
       .5 => 'half',
       ),
       
/*3*/  'extreme floats' => array(
       12.3456789000e6 => 'large',
       12.3456789000E-10 => 'small',
       ),

       // null data
/*4*/ 'null uppercase' => array(
       NULL => 'null 1',
       ), 
       
/*5*/  'null lowercase' => array(
       null => 'null 2',
       ),

       // boolean data
/*6*/ 'bool lowercase' => array(
       true => 'lowert',
       false => 'lowerf',
       ),
       
/*7*/  'bool uppercase' => array(
       TRUE => 'uppert',
       FALSE => 'upperf',
       ),
       
       // empty data
/*8*/ 'empty double quotes' => array(
       "" => 'emptyd',
       ),
       
/*9*/  'empty single quotes' => array(
       '' => 'emptys',
       ),

       // string data
/*10*/ 'string' => array(
       "stringd" => 'stringd',
       'strings' => 'strings',
       $heredoc => 'stringh',
       ),

       // undefined data
/*11*/ 'undefined' => array(
       @$undefined_var => 'undefined',
       ),

       // unset data
/*12*/ 'unset' => array(
       @$unset_var => 'unset',
       ),
);

// loop through each element of $inputs to check the behavior of each()
$iterator = 1;
foreach($inputs as $key => $input) {
  echo "\n-- Iteration $iterator: $key data --\n";
  var_dump( each($input) );
  $iterator++;
};

echo "Done";
?>

--EXPECTF--
*** Testing each() : usage variations ***

-- Iteration 1: int data --
array(4) {
  [1]=>
  unicode(4) "zero"
  [u"value"]=>
  unicode(4) "zero"
  [0]=>
  int(0)
  [u"key"]=>
  int(0)
}

-- Iteration 2: float data --
array(4) {
  [1]=>
  unicode(8) "positive"
  [u"value"]=>
  unicode(8) "positive"
  [0]=>
  int(10)
  [u"key"]=>
  int(10)
}

-- Iteration 3: extreme floats data --
array(4) {
  [1]=>
  unicode(5) "large"
  [u"value"]=>
  unicode(5) "large"
  [0]=>
  int(12345678)
  [u"key"]=>
  int(12345678)
}

-- Iteration 4: null uppercase data --
array(4) {
  [1]=>
  unicode(6) "null 1"
  [u"value"]=>
  unicode(6) "null 1"
  [0]=>
  unicode(0) ""
  [u"key"]=>
  unicode(0) ""
}

-- Iteration 5: null lowercase data --
array(4) {
  [1]=>
  unicode(6) "null 2"
  [u"value"]=>
  unicode(6) "null 2"
  [0]=>
  unicode(0) ""
  [u"key"]=>
  unicode(0) ""
}

-- Iteration 6: bool lowercase data --
array(4) {
  [1]=>
  unicode(6) "lowert"
  [u"value"]=>
  unicode(6) "lowert"
  [0]=>
  int(1)
  [u"key"]=>
  int(1)
}

-- Iteration 7: bool uppercase data --
array(4) {
  [1]=>
  unicode(6) "uppert"
  [u"value"]=>
  unicode(6) "uppert"
  [0]=>
  int(1)
  [u"key"]=>
  int(1)
}

-- Iteration 8: empty double quotes data --
array(4) {
  [1]=>
  unicode(6) "emptyd"
  [u"value"]=>
  unicode(6) "emptyd"
  [0]=>
  unicode(0) ""
  [u"key"]=>
  unicode(0) ""
}

-- Iteration 9: empty single quotes data --
array(4) {
  [1]=>
  unicode(6) "emptys"
  [u"value"]=>
  unicode(6) "emptys"
  [0]=>
  unicode(0) ""
  [u"key"]=>
  unicode(0) ""
}

-- Iteration 10: string data --
array(4) {
  [1]=>
  unicode(7) "stringd"
  [u"value"]=>
  unicode(7) "stringd"
  [0]=>
  unicode(7) "stringd"
  [u"key"]=>
  unicode(7) "stringd"
}

-- Iteration 11: undefined data --
array(4) {
  [1]=>
  unicode(9) "undefined"
  [u"value"]=>
  unicode(9) "undefined"
  [0]=>
  unicode(0) ""
  [u"key"]=>
  unicode(0) ""
}

-- Iteration 12: unset data --
array(4) {
  [1]=>
  unicode(5) "unset"
  [u"value"]=>
  unicode(5) "unset"
  [0]=>
  unicode(0) ""
  [u"key"]=>
  unicode(0) ""
}
Done