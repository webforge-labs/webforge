<?php

namespace Webforge\Code\Generator;

use Webforge\Common\ArrayUtil as A;

class GFunctionBodyTest extends \Webforge\Code\Test\Base {
  
  protected $body;
  
  public function setUp() {
  }
  
  /**
   * @dataProvider phpBodyExamples
   */
  public function testPHPCodeEqualsArrayLinesAcception(Array $lines) {
    $body = GFunctionBody::create($lines);
    
    $this->assertCodeEquals(
      A::join($lines, "%s\n"),
      $body->php(0, "\n")
    );
  }
  
  public static function phpBodyExamples() {
    $tests = array();
    
    $php = function () use (&$tests) {
      $tests[] = array(func_get_args());
    };
    
    $php(
      'return $this->x;'
    );

    $php(
      'if (!isset($this->x)) {',
      '  $this->x = new PointValue(0);',
      '}',
      'return $this->x;'
    );
    
    $php(
      'switch ($var) {',
      NULL,
      "case 'x':",
      '  $this->setX($value);',
      'break;',
      NULL,
      "case 'y':",
      '  $this->setX($value);',
      'break;',
      '}'
    );
    
    return $tests;
  }
}
?>