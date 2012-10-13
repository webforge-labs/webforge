<?php

namespace Webforge\Code\Generator;

use ReflectionClass;

/**
 */
class GClassAbstractMethodStubsTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->baseType =
      GClass::create('baseType')
        ->setAbstract(TRUE)
        ->createMethod('getName')
          ->setAbstract(TRUE)
        ->getGClass();
          
    $this->exportable =
      GInterface::create('Exportable')
        ->createMethod('export')
        ->getGClass();
  }
  
  //public function testGetAllMethods() {
  //  $gClass = new GClass(__NAMESPACE__.'\\ToStubTestClass');
  //  
  //  $toImplement = array();
  //  foreach ($gClass->getAllMethods() as $method) {
  //    if ($method->isAbstract()) {
  //      $toImplement[] = $method->getName();
  //    }
  //  }
  //  
  //  $this->assertArrayEquals(array('implementIt','implementItFromParent','getName','generateDependency'), $toImplement);
  //}

  public function testCreateAbstractMethodStubsFindsInterfacesAndAbstractBaseMethods() {
    
    $gClass = new GClass('ConcreteType');
    $gClass->setParent($this->baseType);
    $gClass->addInterface($this->exportable);
    
    $gClass->createAbstractMethodStubs();
    
    
    $this->testGClass($gClass)
      ->hasMethod('getName')
      ->hasMethod('export')
    ;
  }
  
  public function testAdvancedMethodStubs() {
    $this->markTestIncomplete('@TODO: add an interface to the base type as well!');
  }
}  
?>