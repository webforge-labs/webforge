<?php

namespace Webforge\Code;

use Webforge\Code\Generator\ClassFileMapper;

/**
 */
class GlobalClassFileMapperTest extends \Webforge\Code\Test\Base {
  
  protected $mapper;
  
  public function setUp() {
    $this->mapper = new GlobalClassFileMapper();
  }
  
  public function testThatNonsenseFqnsCantGetFound() {
    $this->setExpectedException('Webforge\Code\ClassFileNotFoundException');
    $this->mapper->getFile('ths\class\has\a\nonsense\name\and\is\not\existent');
  }
  
  public function testEmptyFQNsAreBad() {
    $this->setExpectedException('InvalidArgumentException');
    $this->mapper->getFile('');
  }
  
  public function testAcceptanceForThisTestClass_whichIsAutoloadableByAnComposerProject_whichIsASpecialCaseBecauseItsATest() {
    $file = $this->mapper->getFile(get_class($this));
    
    $this->assertEquals((string) __FILE__, (string) $file);
  }
}
?>