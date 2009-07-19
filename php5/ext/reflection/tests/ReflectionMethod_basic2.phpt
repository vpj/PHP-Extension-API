--TEST--
ReflectionMethod class __toString() and export() methods
--FILE--
<?php

function reflectMethod($class, $method) {
    $methodInfo = new ReflectionMethod($class, $method);
    echo "**********************************\n";
    echo "Reflecting on method $class::$method()\n\n";
    echo "__toString():\n";
    var_dump($methodInfo->__toString());
    echo "\nexport():\n";
    var_dump(ReflectionMethod::export($class, $method, true));
    echo "\n**********************************\n";
}

class TestClass
{
    public function foo() {
        echo "Called foo()\n";
    }

    static function stat() {
        echo "Called stat()\n";
    }

    private function priv() {
        echo "Called priv()\n";
    }

    protected function prot() {}

    public function __destruct() {}
}

class DerivedClass extends TestClass {}

interface TestInterface {
    public function int();
}

reflectMethod("DerivedClass", "foo");
reflectMethod("TestClass", "stat");
reflectMethod("TestClass", "priv");
reflectMethod("TestClass", "prot");
reflectMethod("DerivedClass", "prot");
reflectMethod("TestInterface", "int");
reflectMethod("ReflectionProperty", "__construct");
reflectMethod("TestClass", "__destruct");

?>
--EXPECTF--
**********************************
Reflecting on method DerivedClass::foo()

__toString():
unicode(%d) "Method [ <user, inherits TestClass> public method foo ] {
  @@ %sReflectionMethod_basic2.php 16 - 18
}
"

export():
unicode(%d) "Method [ <user, inherits TestClass> public method foo ] {
  @@ %sReflectionMethod_basic2.php 16 - 18
}
"

**********************************
**********************************
Reflecting on method TestClass::stat()

__toString():
unicode(%d) "Method [ <user> static public method stat ] {
  @@ %sReflectionMethod_basic2.php 20 - 22
}
"

export():
unicode(%d) "Method [ <user> static public method stat ] {
  @@ %sReflectionMethod_basic2.php 20 - 22
}
"

**********************************
**********************************
Reflecting on method TestClass::priv()

__toString():
unicode(%d) "Method [ <user> private method priv ] {
  @@ %sReflectionMethod_basic2.php 24 - 26
}
"

export():
unicode(%d) "Method [ <user> private method priv ] {
  @@ %sReflectionMethod_basic2.php 24 - 26
}
"

**********************************
**********************************
Reflecting on method TestClass::prot()

__toString():
unicode(%d) "Method [ <user> protected method prot ] {
  @@ %sReflectionMethod_basic2.php 28 - 28
}
"

export():
unicode(%d) "Method [ <user> protected method prot ] {
  @@ %sReflectionMethod_basic2.php 28 - 28
}
"

**********************************
**********************************
Reflecting on method DerivedClass::prot()

__toString():
unicode(%d) "Method [ <user, inherits TestClass> protected method prot ] {
  @@ %sReflectionMethod_basic2.php 28 - 28
}
"

export():
unicode(%d) "Method [ <user, inherits TestClass> protected method prot ] {
  @@ %sReflectionMethod_basic2.php 28 - 28
}
"

**********************************
**********************************
Reflecting on method TestInterface::int()

__toString():
unicode(%d) "Method [ <user> abstract public method int ] {
  @@ %sReflectionMethod_basic2.php 36 - 36
}
"

export():
unicode(%d) "Method [ <user> abstract public method int ] {
  @@ %sReflectionMethod_basic2.php 36 - 36
}
"

**********************************
**********************************
Reflecting on method ReflectionProperty::__construct()

__toString():
unicode(137) "Method [ <internal:Reflection, ctor> public method __construct ] {

  - Parameters [1] {
    Parameter #0 [ <required> $argument ]
  }
}
"

export():
unicode(137) "Method [ <internal:Reflection, ctor> public method __construct ] {

  - Parameters [1] {
    Parameter #0 [ <required> $argument ]
  }
}
"

**********************************
**********************************
Reflecting on method TestClass::__destruct()

__toString():
unicode(%d) "Method [ <user, dtor> public method __destruct ] {
  @@ %sReflectionMethod_basic2.php 30 - 30
}
"

export():
unicode(%d) "Method [ <user, dtor> public method __destruct ] {
  @@ %sReflectionMethod_basic2.php 30 - 30
}
"

**********************************
