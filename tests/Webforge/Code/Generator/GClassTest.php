<?php

namespace Webforge\Code\Generator;

class GClassTest extends \Webforge\Code\Test\Base {
  
  protected $gClass;
  
  public function setUp() {
    $this->gClass = new GClass(get_class($this));
    parent::setUp();
  }
  

  public function testConstructIsRobustToWrongPrefixSlashes() {
    $gClass = GClass::create('XML\Object');
    $this->assertEquals('XML',$gClass->getNamespace());
    $this->assertEquals('Object',$gClass->getName());
    $this->assertEquals('XML\Object',$gClass->getFQN());

    $gClass = GClass::create('\XML\Object');
    $this->assertEquals('XML', $gClass->getNamespace());
    $this->assertEquals('Object',$gClass->getName());
    $this->assertEquals('XML\Object',$gClass->getFQN());
    
  }
  
  public function testNamespaceCanBeeReplacedThroughSet() {
    $gClass = GClass::create('XML\Object');
    $gClass->setNamespace('Javascript');
    $this->assertEquals('Javascript', $gClass->getNamespace());
    
    $this->assertEquals('Javascript\Object', $gClass->getFQN());
  }

  public function testWrongNamespaceGetsNormalized() {
    $gClass = GClass::create('XML\Object');
    $gClass->setNamespace('\Wrong\XML');
    
    $this->assertEquals('Wrong\XML\Object', $gClass->getFQN());
  }
  
  public function testFQNAndNotFQNClassesNamespaces() {
    // test looks little weird, but thats the difference from the psc-cms- GClass
    $noFQN = GClass::create('LParameter');
    $this->assertEquals(NULL, $noFQN->getNamespace());
    $this->assertEquals('LParameter', $noFQN->getFQN());
    
    $fqn = GClass::create('\LParameter');
    $this->assertEquals(NULL, $fqn->getNamespace());
    $this->assertEquals('LParameter', $fqn->getName());
  }
  
  public function testImportsCanBeAddedAndRemoved() {
    // most of this is tested in imports
    $this->gClass->addImport(new GClass('Other\UsedClass'));
    $this->assertTrue($this->gClass->hasImport('UsedClass'));
    
    $this->gClass->removeImport(new GClass('Other\UsedClass'));
    $this->assertFalse($this->gClass->hasImport('UsedClass'));
  }
  
  public function testPropertyHintsAreImported() {
    $this->markTestIncomplete('Blocker: properties in psc-cms do not have a type(!) change this');
  }

  public function testMethodParameterHintsAreImported() {
    $this->markTestIncomplete('TODO: this is not implemented yet');
  }

  public function testInterfacesClassesAreImported() {
    $this->markTestIncomplete('todo: interfaces should be importet');
  }

  public function testParentClassIsImported() {
    // should the parent
    $this->markTestIncomplete('parentClass should be imported');
  }
}
?>