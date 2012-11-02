<?php

namespace Webforge\Code\Generator;

use Psc\A;

class GFunctionBodyPrettyPrintTest extends \Webforge\Code\Test\Base {
  
  protected $body;
  
  public function setUp() {
  }
  
  /**
   * @dataProvider phpIndentedBodyExamples
   */
  public function testPHPCodeEqualsArrayLinesIndentation($phpCode) {
    $body = GFunctionBody::create($phpCode);
    
    $this->assertEquals(
      $phpCode,
      $body->php(0, "\n")
    );
  }
  
  public static function phpIndentedBodyExamples() {
    $tests = array();
    
    $php = function ($phpCode) use (&$tests) {
      $tests[] = array($phpCode);
    };

    $php(<<<'PHP'
$myVar = 8; // 0-based
$otherVar = 9; // 1-based
PHP
    );

    
    $php(
        "if (true) {\n"
      . "  //just a comment\n"
      . "}"
    );

    //5.1:  if then else
    $php(
<<<'PHP'
if ($expr1) {
    $comment = '// if body';
} elseif ($expr2) {
    $comment = '// elseif body';
} else {
    $comment = '// else body;';
}
PHP
    );
    

    // 5.2. switch, case
    $php(
<<<'PHP'
switch ($expr) {
    case 0:
        echo 'First case, with a break';
        break;
    case 1:
        echo 'Second case, which falls through';
    case 2:
    case 3:
    case 4:
        echo 'Third case, return instead of break';
        return;
    default:
        echo 'Default case';
        break;
}
PHP
  );

    
    //5.3. while, do while
    $php(
<<<'PHP'
while ($expr) {
    $comment = 'structure body';
}
PHP
    );

    $php(
<<<'PHP'
do {
    $comment = 'structure body';
} while ($expr);
PHP
  );

    // 5.4 for
    $php(
<<<'PHP'
for ($i = 0; $i < 10; $i++) {
    // body
}
PHP
    );

  // 5.5 foreach   
    $php(
<<<'PHP'
foreach ($iterable as $key => $value) {
    $comment = 'foreach body';
}
PHP
    );

    // 5.6 try catch
    $php(
<<<'PHP'
try {
    $comment = 'try body';
} catch (FirstExceptionType $e) {
    $comment = 'catch body';
} catch (OtherExceptionType $e) {
    $comment = 'catch body';
}
PHP
    );
  
  // 5.7 Closures
    $php(
<<<'PHP'
$closureWithArgs = function ($arg1, $arg2) {
    $comment = 'closure body';
};
$closureWithArgsAndVars = function ($arg1, $arg2) use ($var1, $var2) {
    $comment = 'closure body';
};
PHP
    );
    
    return $tests;
    





    $php(
<<<'PHP'
parent::__construct(
    $subNodes + array(
        'stmts'  => array(),
        'params' => array(),
        'uses'   => array(),
        'byRef'  => false,
        'static' => false,
    ),
    $attributes
);
PHP
    );

  // mixed call + closure
    $php(
<<<'PHP'
$foo->bar(
    $arg1,
    function ($arg2) use ($var1) {
        ($comment = 'closure body');
    },
    $arg3
);
PHP
    );
  
    return $tests;
  }
}
?>