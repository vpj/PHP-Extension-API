--TEST--
Testing array_slice() function
--FILE--
<?php

$var_array = array(
                   array(),
                   array(1,2,3,4,5,6,7,8,9),
                   array("One", "Two", "Three", "Four", "Five"),
                   array(6, "six", 7, "seven", 8, "eight", 9, "nine"),
                   array( "a" => "aaa", "A" => "AAA", "c" => "ccc", "d" => "ddd", "e" => "eee"),
                   array("1" => "one", "2" => "two", "3" => "three", "4" => "four", "5" => "five"),
                   array(1 => "one", 2 => "two", 3 => 7, 4 => "four", 5 => "five"),
                   array("f" => "fff", "1" => "one", 4 => 6, "" => "blank", 2.4 => "float", "F" => "FFF",
                         "blank" => "", 3.7 => 3.7, 5.4 => 7, 6 => 8.6, '5' => "Five"),
                   array(12, "name", 'age', '45'),
                   array( array("oNe", "tWo", 4), array(10, 20, 30, 40, 50), array())
                 );

$num = 4;
$str = "john";

/* Zero args */
echo"\n*** Output for Zero Argument ***\n";
array_slice();

/* Single args */
echo"\n*** Output for Single array Argument ***\n";
array_slice($var_array);

/* More than valid no. of args (ie. >4 )  */
echo"\n*** Output for invalid number of Arguments ***\n";
array_slice($var_array, 2, 4, true, 3);

/* Scalar arg */
echo"\n*** Output for scalar Argument ***\n";
array_slice($num, 2);

/* String arg */
echo"\n*** Output for string Argument ***\n";
array_slice($str, 2);

$counter = 1;
foreach ($var_array as $sub_array)
{
  /* variations with two arguments */
  /* offset values >, < and = 0    */

  echo"\n*** Iteration ".$counter." ***\n";
  echo"\n*** Variation with first two Arguments ***\n";
  var_dump ( array_slice($sub_array, 1) );
  var_dump ( array_slice($sub_array, 0) );
  var_dump ( array_slice($sub_array, -2) );

  /* variations with three arguments */
  /* offset value variations with length values  */
  echo"\n*** Variation with first three Arguments ***\n";
  var_dump ( array_slice($sub_array, 1, 3) );
  var_dump ( array_slice($sub_array, 1, 0) );
  var_dump ( array_slice($sub_array, 1, -3) );
  var_dump ( array_slice($sub_array, 0, 3) );
  var_dump ( array_slice($sub_array, 0, 0) );
  var_dump ( array_slice($sub_array, 0, -3) );
  var_dump ( array_slice($sub_array, -2, 3) );
  var_dump ( array_slice($sub_array, -2, 0 ) );
  var_dump ( array_slice($sub_array, -2, -3) );

  /* variations with four arguments */
  /* offset value, length value and preserve_key values variation */
  echo"\n*** Variation with first two arguments with preserve_key value TRUE ***\n";
  var_dump ( array_slice($sub_array, 1, 3, true) );
  var_dump ( array_slice($sub_array, 1, 0, true) );
  var_dump ( array_slice($sub_array, 1, -3, true) );
  var_dump ( array_slice($sub_array, 0, 3, true) );
  var_dump ( array_slice($sub_array, 0, 0, true) );
  var_dump ( array_slice($sub_array, 0, -3, true) );
  var_dump ( array_slice($sub_array, -2, 3, true) );
  var_dump ( array_slice($sub_array, -2, 0, true) );
  var_dump ( array_slice($sub_array, -2, -3, true) );
  $counter++;
}

  /* variation of offset and length to point to same element */
  echo"\n*** Typical Variation of offset and length  Arguments ***\n";
  var_dump (array_slice($var_array[2], 1, -3, true) );
  var_dump (array_slice($var_array[2], 1, -3, false) );
  var_dump (array_slice($var_array[2], -3, -2, true) );
  var_dump (array_slice($var_array[2], -3, -2, false) );

?>
--EXPECTF--
*** Output for Zero Argument ***

Warning: array_slice() expects at least 2 parameters, 0 given in %s on line %d

*** Output for Single array Argument ***

Warning: array_slice() expects at least 2 parameters, 1 given in %s on line %d

*** Output for invalid number of Arguments ***

Warning: array_slice() expects at most 4 parameters, 5 given in %s on line %d

*** Output for scalar Argument ***

Warning: array_slice() expects parameter 1 to be array, integer given in %s on line %d

*** Output for string Argument ***

Warning: array_slice() expects parameter 1 to be array, Unicode string given in %s on line %d

*** Iteration 1 ***

*** Variation with first two Arguments ***
array(0) {
}
array(0) {
}
array(0) {
}

*** Variation with first three Arguments ***
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}

*** Variation with first two arguments with preserve_key value TRUE ***
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}
array(0) {
}

*** Iteration 2 ***

*** Variation with first two Arguments ***
array(8) {
  [0]=>
  int(2)
  [1]=>
  int(3)
  [2]=>
  int(4)
  [3]=>
  int(5)
  [4]=>
  int(6)
  [5]=>
  int(7)
  [6]=>
  int(8)
  [7]=>
  int(9)
}
array(9) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
  [3]=>
  int(4)
  [4]=>
  int(5)
  [5]=>
  int(6)
  [6]=>
  int(7)
  [7]=>
  int(8)
  [8]=>
  int(9)
}
array(2) {
  [0]=>
  int(8)
  [1]=>
  int(9)
}

*** Variation with first three Arguments ***
array(3) {
  [0]=>
  int(2)
  [1]=>
  int(3)
  [2]=>
  int(4)
}
array(0) {
}
array(5) {
  [0]=>
  int(2)
  [1]=>
  int(3)
  [2]=>
  int(4)
  [3]=>
  int(5)
  [4]=>
  int(6)
}
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
}
array(0) {
}
array(6) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
  [3]=>
  int(4)
  [4]=>
  int(5)
  [5]=>
  int(6)
}
array(2) {
  [0]=>
  int(8)
  [1]=>
  int(9)
}
array(0) {
}
array(0) {
}

*** Variation with first two arguments with preserve_key value TRUE ***
array(3) {
  [1]=>
  int(2)
  [2]=>
  int(3)
  [3]=>
  int(4)
}
array(0) {
}
array(5) {
  [1]=>
  int(2)
  [2]=>
  int(3)
  [3]=>
  int(4)
  [4]=>
  int(5)
  [5]=>
  int(6)
}
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
}
array(0) {
}
array(6) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
  [3]=>
  int(4)
  [4]=>
  int(5)
  [5]=>
  int(6)
}
array(2) {
  [7]=>
  int(8)
  [8]=>
  int(9)
}
array(0) {
}
array(0) {
}

*** Iteration 3 ***

*** Variation with first two Arguments ***
array(4) {
  [0]=>
  unicode(3) "Two"
  [1]=>
  unicode(5) "Three"
  [2]=>
  unicode(4) "Four"
  [3]=>
  unicode(4) "Five"
}
array(5) {
  [0]=>
  unicode(3) "One"
  [1]=>
  unicode(3) "Two"
  [2]=>
  unicode(5) "Three"
  [3]=>
  unicode(4) "Four"
  [4]=>
  unicode(4) "Five"
}
array(2) {
  [0]=>
  unicode(4) "Four"
  [1]=>
  unicode(4) "Five"
}

*** Variation with first three Arguments ***
array(3) {
  [0]=>
  unicode(3) "Two"
  [1]=>
  unicode(5) "Three"
  [2]=>
  unicode(4) "Four"
}
array(0) {
}
array(1) {
  [0]=>
  unicode(3) "Two"
}
array(3) {
  [0]=>
  unicode(3) "One"
  [1]=>
  unicode(3) "Two"
  [2]=>
  unicode(5) "Three"
}
array(0) {
}
array(2) {
  [0]=>
  unicode(3) "One"
  [1]=>
  unicode(3) "Two"
}
array(2) {
  [0]=>
  unicode(4) "Four"
  [1]=>
  unicode(4) "Five"
}
array(0) {
}
array(0) {
}

*** Variation with first two arguments with preserve_key value TRUE ***
array(3) {
  [1]=>
  unicode(3) "Two"
  [2]=>
  unicode(5) "Three"
  [3]=>
  unicode(4) "Four"
}
array(0) {
}
array(1) {
  [1]=>
  unicode(3) "Two"
}
array(3) {
  [0]=>
  unicode(3) "One"
  [1]=>
  unicode(3) "Two"
  [2]=>
  unicode(5) "Three"
}
array(0) {
}
array(2) {
  [0]=>
  unicode(3) "One"
  [1]=>
  unicode(3) "Two"
}
array(2) {
  [3]=>
  unicode(4) "Four"
  [4]=>
  unicode(4) "Five"
}
array(0) {
}
array(0) {
}

*** Iteration 4 ***

*** Variation with first two Arguments ***
array(7) {
  [0]=>
  unicode(3) "six"
  [1]=>
  int(7)
  [2]=>
  unicode(5) "seven"
  [3]=>
  int(8)
  [4]=>
  unicode(5) "eight"
  [5]=>
  int(9)
  [6]=>
  unicode(4) "nine"
}
array(8) {
  [0]=>
  int(6)
  [1]=>
  unicode(3) "six"
  [2]=>
  int(7)
  [3]=>
  unicode(5) "seven"
  [4]=>
  int(8)
  [5]=>
  unicode(5) "eight"
  [6]=>
  int(9)
  [7]=>
  unicode(4) "nine"
}
array(2) {
  [0]=>
  int(9)
  [1]=>
  unicode(4) "nine"
}

*** Variation with first three Arguments ***
array(3) {
  [0]=>
  unicode(3) "six"
  [1]=>
  int(7)
  [2]=>
  unicode(5) "seven"
}
array(0) {
}
array(4) {
  [0]=>
  unicode(3) "six"
  [1]=>
  int(7)
  [2]=>
  unicode(5) "seven"
  [3]=>
  int(8)
}
array(3) {
  [0]=>
  int(6)
  [1]=>
  unicode(3) "six"
  [2]=>
  int(7)
}
array(0) {
}
array(5) {
  [0]=>
  int(6)
  [1]=>
  unicode(3) "six"
  [2]=>
  int(7)
  [3]=>
  unicode(5) "seven"
  [4]=>
  int(8)
}
array(2) {
  [0]=>
  int(9)
  [1]=>
  unicode(4) "nine"
}
array(0) {
}
array(0) {
}

*** Variation with first two arguments with preserve_key value TRUE ***
array(3) {
  [1]=>
  unicode(3) "six"
  [2]=>
  int(7)
  [3]=>
  unicode(5) "seven"
}
array(0) {
}
array(4) {
  [1]=>
  unicode(3) "six"
  [2]=>
  int(7)
  [3]=>
  unicode(5) "seven"
  [4]=>
  int(8)
}
array(3) {
  [0]=>
  int(6)
  [1]=>
  unicode(3) "six"
  [2]=>
  int(7)
}
array(0) {
}
array(5) {
  [0]=>
  int(6)
  [1]=>
  unicode(3) "six"
  [2]=>
  int(7)
  [3]=>
  unicode(5) "seven"
  [4]=>
  int(8)
}
array(2) {
  [6]=>
  int(9)
  [7]=>
  unicode(4) "nine"
}
array(0) {
}
array(0) {
}

*** Iteration 5 ***

*** Variation with first two Arguments ***
array(4) {
  [u"A"]=>
  unicode(3) "AAA"
  [u"c"]=>
  unicode(3) "ccc"
  [u"d"]=>
  unicode(3) "ddd"
  [u"e"]=>
  unicode(3) "eee"
}
array(5) {
  [u"a"]=>
  unicode(3) "aaa"
  [u"A"]=>
  unicode(3) "AAA"
  [u"c"]=>
  unicode(3) "ccc"
  [u"d"]=>
  unicode(3) "ddd"
  [u"e"]=>
  unicode(3) "eee"
}
array(2) {
  [u"d"]=>
  unicode(3) "ddd"
  [u"e"]=>
  unicode(3) "eee"
}

*** Variation with first three Arguments ***
array(3) {
  [u"A"]=>
  unicode(3) "AAA"
  [u"c"]=>
  unicode(3) "ccc"
  [u"d"]=>
  unicode(3) "ddd"
}
array(0) {
}
array(1) {
  [u"A"]=>
  unicode(3) "AAA"
}
array(3) {
  [u"a"]=>
  unicode(3) "aaa"
  [u"A"]=>
  unicode(3) "AAA"
  [u"c"]=>
  unicode(3) "ccc"
}
array(0) {
}
array(2) {
  [u"a"]=>
  unicode(3) "aaa"
  [u"A"]=>
  unicode(3) "AAA"
}
array(2) {
  [u"d"]=>
  unicode(3) "ddd"
  [u"e"]=>
  unicode(3) "eee"
}
array(0) {
}
array(0) {
}

*** Variation with first two arguments with preserve_key value TRUE ***
array(3) {
  [u"A"]=>
  unicode(3) "AAA"
  [u"c"]=>
  unicode(3) "ccc"
  [u"d"]=>
  unicode(3) "ddd"
}
array(0) {
}
array(1) {
  [u"A"]=>
  unicode(3) "AAA"
}
array(3) {
  [u"a"]=>
  unicode(3) "aaa"
  [u"A"]=>
  unicode(3) "AAA"
  [u"c"]=>
  unicode(3) "ccc"
}
array(0) {
}
array(2) {
  [u"a"]=>
  unicode(3) "aaa"
  [u"A"]=>
  unicode(3) "AAA"
}
array(2) {
  [u"d"]=>
  unicode(3) "ddd"
  [u"e"]=>
  unicode(3) "eee"
}
array(0) {
}
array(0) {
}

*** Iteration 6 ***

*** Variation with first two Arguments ***
array(4) {
  [0]=>
  unicode(3) "two"
  [1]=>
  unicode(5) "three"
  [2]=>
  unicode(4) "four"
  [3]=>
  unicode(4) "five"
}
array(5) {
  [0]=>
  unicode(3) "one"
  [1]=>
  unicode(3) "two"
  [2]=>
  unicode(5) "three"
  [3]=>
  unicode(4) "four"
  [4]=>
  unicode(4) "five"
}
array(2) {
  [0]=>
  unicode(4) "four"
  [1]=>
  unicode(4) "five"
}

*** Variation with first three Arguments ***
array(3) {
  [0]=>
  unicode(3) "two"
  [1]=>
  unicode(5) "three"
  [2]=>
  unicode(4) "four"
}
array(0) {
}
array(1) {
  [0]=>
  unicode(3) "two"
}
array(3) {
  [0]=>
  unicode(3) "one"
  [1]=>
  unicode(3) "two"
  [2]=>
  unicode(5) "three"
}
array(0) {
}
array(2) {
  [0]=>
  unicode(3) "one"
  [1]=>
  unicode(3) "two"
}
array(2) {
  [0]=>
  unicode(4) "four"
  [1]=>
  unicode(4) "five"
}
array(0) {
}
array(0) {
}

*** Variation with first two arguments with preserve_key value TRUE ***
array(3) {
  [2]=>
  unicode(3) "two"
  [3]=>
  unicode(5) "three"
  [4]=>
  unicode(4) "four"
}
array(0) {
}
array(1) {
  [2]=>
  unicode(3) "two"
}
array(3) {
  [1]=>
  unicode(3) "one"
  [2]=>
  unicode(3) "two"
  [3]=>
  unicode(5) "three"
}
array(0) {
}
array(2) {
  [1]=>
  unicode(3) "one"
  [2]=>
  unicode(3) "two"
}
array(2) {
  [4]=>
  unicode(4) "four"
  [5]=>
  unicode(4) "five"
}
array(0) {
}
array(0) {
}

*** Iteration 7 ***

*** Variation with first two Arguments ***
array(4) {
  [0]=>
  unicode(3) "two"
  [1]=>
  int(7)
  [2]=>
  unicode(4) "four"
  [3]=>
  unicode(4) "five"
}
array(5) {
  [0]=>
  unicode(3) "one"
  [1]=>
  unicode(3) "two"
  [2]=>
  int(7)
  [3]=>
  unicode(4) "four"
  [4]=>
  unicode(4) "five"
}
array(2) {
  [0]=>
  unicode(4) "four"
  [1]=>
  unicode(4) "five"
}

*** Variation with first three Arguments ***
array(3) {
  [0]=>
  unicode(3) "two"
  [1]=>
  int(7)
  [2]=>
  unicode(4) "four"
}
array(0) {
}
array(1) {
  [0]=>
  unicode(3) "two"
}
array(3) {
  [0]=>
  unicode(3) "one"
  [1]=>
  unicode(3) "two"
  [2]=>
  int(7)
}
array(0) {
}
array(2) {
  [0]=>
  unicode(3) "one"
  [1]=>
  unicode(3) "two"
}
array(2) {
  [0]=>
  unicode(4) "four"
  [1]=>
  unicode(4) "five"
}
array(0) {
}
array(0) {
}

*** Variation with first two arguments with preserve_key value TRUE ***
array(3) {
  [2]=>
  unicode(3) "two"
  [3]=>
  int(7)
  [4]=>
  unicode(4) "four"
}
array(0) {
}
array(1) {
  [2]=>
  unicode(3) "two"
}
array(3) {
  [1]=>
  unicode(3) "one"
  [2]=>
  unicode(3) "two"
  [3]=>
  int(7)
}
array(0) {
}
array(2) {
  [1]=>
  unicode(3) "one"
  [2]=>
  unicode(3) "two"
}
array(2) {
  [4]=>
  unicode(4) "four"
  [5]=>
  unicode(4) "five"
}
array(0) {
}
array(0) {
}

*** Iteration 8 ***

*** Variation with first two Arguments ***
array(9) {
  [0]=>
  unicode(3) "one"
  [1]=>
  int(6)
  [u""]=>
  unicode(5) "blank"
  [2]=>
  unicode(5) "float"
  [u"F"]=>
  unicode(3) "FFF"
  [u"blank"]=>
  unicode(0) ""
  [3]=>
  float(3.7)
  [4]=>
  unicode(4) "Five"
  [5]=>
  float(8.6)
}
array(10) {
  [u"f"]=>
  unicode(3) "fff"
  [0]=>
  unicode(3) "one"
  [1]=>
  int(6)
  [u""]=>
  unicode(5) "blank"
  [2]=>
  unicode(5) "float"
  [u"F"]=>
  unicode(3) "FFF"
  [u"blank"]=>
  unicode(0) ""
  [3]=>
  float(3.7)
  [4]=>
  unicode(4) "Five"
  [5]=>
  float(8.6)
}
array(2) {
  [0]=>
  unicode(4) "Five"
  [1]=>
  float(8.6)
}

*** Variation with first three Arguments ***
array(3) {
  [0]=>
  unicode(3) "one"
  [1]=>
  int(6)
  [u""]=>
  unicode(5) "blank"
}
array(0) {
}
array(6) {
  [0]=>
  unicode(3) "one"
  [1]=>
  int(6)
  [u""]=>
  unicode(5) "blank"
  [2]=>
  unicode(5) "float"
  [u"F"]=>
  unicode(3) "FFF"
  [u"blank"]=>
  unicode(0) ""
}
array(3) {
  [u"f"]=>
  unicode(3) "fff"
  [0]=>
  unicode(3) "one"
  [1]=>
  int(6)
}
array(0) {
}
array(7) {
  [u"f"]=>
  unicode(3) "fff"
  [0]=>
  unicode(3) "one"
  [1]=>
  int(6)
  [u""]=>
  unicode(5) "blank"
  [2]=>
  unicode(5) "float"
  [u"F"]=>
  unicode(3) "FFF"
  [u"blank"]=>
  unicode(0) ""
}
array(2) {
  [0]=>
  unicode(4) "Five"
  [1]=>
  float(8.6)
}
array(0) {
}
array(0) {
}

*** Variation with first two arguments with preserve_key value TRUE ***
array(3) {
  [1]=>
  unicode(3) "one"
  [4]=>
  int(6)
  [u""]=>
  unicode(5) "blank"
}
array(0) {
}
array(6) {
  [1]=>
  unicode(3) "one"
  [4]=>
  int(6)
  [u""]=>
  unicode(5) "blank"
  [2]=>
  unicode(5) "float"
  [u"F"]=>
  unicode(3) "FFF"
  [u"blank"]=>
  unicode(0) ""
}
array(3) {
  [u"f"]=>
  unicode(3) "fff"
  [1]=>
  unicode(3) "one"
  [4]=>
  int(6)
}
array(0) {
}
array(7) {
  [u"f"]=>
  unicode(3) "fff"
  [1]=>
  unicode(3) "one"
  [4]=>
  int(6)
  [u""]=>
  unicode(5) "blank"
  [2]=>
  unicode(5) "float"
  [u"F"]=>
  unicode(3) "FFF"
  [u"blank"]=>
  unicode(0) ""
}
array(2) {
  [5]=>
  unicode(4) "Five"
  [6]=>
  float(8.6)
}
array(0) {
}
array(0) {
}

*** Iteration 9 ***

*** Variation with first two Arguments ***
array(3) {
  [0]=>
  unicode(4) "name"
  [1]=>
  unicode(3) "age"
  [2]=>
  unicode(2) "45"
}
array(4) {
  [0]=>
  int(12)
  [1]=>
  unicode(4) "name"
  [2]=>
  unicode(3) "age"
  [3]=>
  unicode(2) "45"
}
array(2) {
  [0]=>
  unicode(3) "age"
  [1]=>
  unicode(2) "45"
}

*** Variation with first three Arguments ***
array(3) {
  [0]=>
  unicode(4) "name"
  [1]=>
  unicode(3) "age"
  [2]=>
  unicode(2) "45"
}
array(0) {
}
array(0) {
}
array(3) {
  [0]=>
  int(12)
  [1]=>
  unicode(4) "name"
  [2]=>
  unicode(3) "age"
}
array(0) {
}
array(1) {
  [0]=>
  int(12)
}
array(2) {
  [0]=>
  unicode(3) "age"
  [1]=>
  unicode(2) "45"
}
array(0) {
}
array(0) {
}

*** Variation with first two arguments with preserve_key value TRUE ***
array(3) {
  [1]=>
  unicode(4) "name"
  [2]=>
  unicode(3) "age"
  [3]=>
  unicode(2) "45"
}
array(0) {
}
array(0) {
}
array(3) {
  [0]=>
  int(12)
  [1]=>
  unicode(4) "name"
  [2]=>
  unicode(3) "age"
}
array(0) {
}
array(1) {
  [0]=>
  int(12)
}
array(2) {
  [2]=>
  unicode(3) "age"
  [3]=>
  unicode(2) "45"
}
array(0) {
}
array(0) {
}

*** Iteration 10 ***

*** Variation with first two Arguments ***
array(2) {
  [0]=>
  array(5) {
    [0]=>
    int(10)
    [1]=>
    int(20)
    [2]=>
    int(30)
    [3]=>
    int(40)
    [4]=>
    int(50)
  }
  [1]=>
  array(0) {
  }
}
array(3) {
  [0]=>
  array(3) {
    [0]=>
    unicode(3) "oNe"
    [1]=>
    unicode(3) "tWo"
    [2]=>
    int(4)
  }
  [1]=>
  array(5) {
    [0]=>
    int(10)
    [1]=>
    int(20)
    [2]=>
    int(30)
    [3]=>
    int(40)
    [4]=>
    int(50)
  }
  [2]=>
  array(0) {
  }
}
array(2) {
  [0]=>
  array(5) {
    [0]=>
    int(10)
    [1]=>
    int(20)
    [2]=>
    int(30)
    [3]=>
    int(40)
    [4]=>
    int(50)
  }
  [1]=>
  array(0) {
  }
}

*** Variation with first three Arguments ***
array(2) {
  [0]=>
  array(5) {
    [0]=>
    int(10)
    [1]=>
    int(20)
    [2]=>
    int(30)
    [3]=>
    int(40)
    [4]=>
    int(50)
  }
  [1]=>
  array(0) {
  }
}
array(0) {
}
array(0) {
}
array(3) {
  [0]=>
  array(3) {
    [0]=>
    unicode(3) "oNe"
    [1]=>
    unicode(3) "tWo"
    [2]=>
    int(4)
  }
  [1]=>
  array(5) {
    [0]=>
    int(10)
    [1]=>
    int(20)
    [2]=>
    int(30)
    [3]=>
    int(40)
    [4]=>
    int(50)
  }
  [2]=>
  array(0) {
  }
}
array(0) {
}
array(0) {
}
array(2) {
  [0]=>
  array(5) {
    [0]=>
    int(10)
    [1]=>
    int(20)
    [2]=>
    int(30)
    [3]=>
    int(40)
    [4]=>
    int(50)
  }
  [1]=>
  array(0) {
  }
}
array(0) {
}
array(0) {
}

*** Variation with first two arguments with preserve_key value TRUE ***
array(2) {
  [1]=>
  array(5) {
    [0]=>
    int(10)
    [1]=>
    int(20)
    [2]=>
    int(30)
    [3]=>
    int(40)
    [4]=>
    int(50)
  }
  [2]=>
  array(0) {
  }
}
array(0) {
}
array(0) {
}
array(3) {
  [0]=>
  array(3) {
    [0]=>
    unicode(3) "oNe"
    [1]=>
    unicode(3) "tWo"
    [2]=>
    int(4)
  }
  [1]=>
  array(5) {
    [0]=>
    int(10)
    [1]=>
    int(20)
    [2]=>
    int(30)
    [3]=>
    int(40)
    [4]=>
    int(50)
  }
  [2]=>
  array(0) {
  }
}
array(0) {
}
array(0) {
}
array(2) {
  [1]=>
  array(5) {
    [0]=>
    int(10)
    [1]=>
    int(20)
    [2]=>
    int(30)
    [3]=>
    int(40)
    [4]=>
    int(50)
  }
  [2]=>
  array(0) {
  }
}
array(0) {
}
array(0) {
}

*** Typical Variation of offset and length  Arguments ***
array(1) {
  [1]=>
  unicode(3) "Two"
}
array(1) {
  [0]=>
  unicode(3) "Two"
}
array(1) {
  [2]=>
  unicode(5) "Three"
}
array(1) {
  [0]=>
  unicode(5) "Three"
}
