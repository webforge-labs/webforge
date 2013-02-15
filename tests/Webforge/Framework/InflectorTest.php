<?php

namespace Webforge\Framework;

class InflectorTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\Inflector';
    parent::setUp();
    
    $this->inflector = new Inflector();
  }
  
  /**
   * @dataProvider provideString2Namespace
   */
  public function testNamespaceify($string, $namespace) {
    $this->assertEquals(
      $namespace,
      $this->inflector->namespaceify($string)
    );
  }
  
  public static function provideString2Namespace() {
    $tests = array();
    
    $tests[] = array(
      'some-Slug',
      'SomeSlug'
    );
    
    $tests[] = array(
      'comun',
      'Comun'
    );
    
    return $tests;
  }
}
?>