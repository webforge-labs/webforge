<?php

namespace Webforge\Code\Generator;

use Psc\A;
use Webforge\Common\System\File;
use Webforge\TestData\PHPCodeStandard\PSR\CodeIndentation;
use PHPParser_Lexer;
use PHPParser_Parser;

class GFunctionBodyPrettyPrintTest extends \Webforge\Code\Test\Base {
  
  protected $body;
  
  public function setUp() {
  }
  
  /**
   * @dataProvider providePSRCases
   */
  public function testStringTokenStreamConstructsTheCodeAgain($php) {
    $stks = new StringTokenStream($php);
    
    $constructedPHP = '';
    foreach ($stks as $token) {
      $constructedPHP .= $token;
    }
    
    $this->assertSame(
      $php,
      $constructedPHP
    );
  }
  
  /**
   * @dataProvider providePSRCases
   */
  public function testParserWithLexerWithOffsets_ConstructsTheCodeAgain_whenNothingIsChanged($php) {
    $lexer = new LexerWithTokenOffsets();
    $parser = new PHPParser_Parser($lexer);
    $tokens = $parser->parse($php);
    
    $prettyPrinter = new PrettyPrinter();
    $prettyPrinter->inphp = $php;
    
    $actualPHP = $prettyPrinter->prettyPrint($tokens);    
    
    $this->assertEquals(
      $php,
      $actualPHP
    );
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
  
  public function testPHPCodeEqualsArrayLinesIndentationWithComments() {
    $this->markTestIncomplete('blocking errors with php parser');
    // comments are not parsed correctly
    // inline comments are not assigned correctly, etc
  }
  
  public static function providePSRCases() {
    $tests = array();
    
    foreach (CodeIndentation::getAllCases() as $fqn) {
      $case = new $fqn();
      
      $tests[] = array(
        "<?php\n".$case->toPSR2(),
        "<?php\n".$case->toString()
      );
    }
    
    $php = <<<'PHP'
<?php
if ($expr) {
    // body comment
}
PHP;
    
    $tests[] = array($php, $php);
    
    return $tests;
  }
  
  public static function phpIndentedBodyExamples() {
    $tests = array();
    
    $php = function ($phpCode) use (&$tests) {
      $tests[] = array($phpCode);
    };

    $php(
        "if (true) {\n"
      . "    \$c = 'just a comment';\n"
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
    $comment = 'body';
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
  }
}
?>