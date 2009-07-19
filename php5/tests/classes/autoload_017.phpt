--TEST--
Ensure ReflectionClass::implementsInterface triggers autoload.
--SKIPIF--
<?php extension_loaded('reflection') or die('skip'); ?>
--FILE--
<?php
  function __autoload($name)
  {
      echo "In autoload: ";
      var_dump($name);
  }

  $rc = new ReflectionClass("stdClass");
  
  try {
    $rc->implementsInterface("UndefI");
  } catch (ReflectionException $e) {
    echo $e->getMessage();
  }
?>
--EXPECT--
In autoload: unicode(6) "UndefI"
Interface UndefI does not exist
