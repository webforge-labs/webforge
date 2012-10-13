<?php

namespace Webforge\Code\Generator;

class GClassTest extends \Webforge\Code\Test\Base {
  
  protected $gClass;
  
  public function setUp() {
    $this->gClass = new GClass(get_class($this));
    
    $this->exportable = new GClass('Exportable');
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
  
  public function testThatParentCanBeSet() {
    $gClass = GClass::create('Psc\Code\SpecificGenerator');
    
    $this->assertInstanceOf('Webforge\Code\Generator\GClass',
                            $gClass->setParent($parent = GClass::create('Psc\Code\Generator'))
                           );
    
    $this->assertSame($parent, $gClass->getParent());
  }
  
  public function testNamespaceCanBeReplacedThroughSet() {
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
  
  public function testPropertyTypesAsHintsForClassesAreImported() {
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

  public function testNewInstance() {
    $gClass = new GClass('Psc\Exception');
    $exception = $gClass->newInstance(array('just a test error'));
    
    $this->assertInstanceOf('Psc\Exception', $exception);
    $this->assertEquals('just a test error', $exception->getMessage());
  }
  
  public function testGetReflection() {
    $this->assertInstanceOf('ReflectionClass', $this->gClass->getReflection());
  }
  
  public function testNewInstanceWithoutConstructor() {
    $gClass = new GClass('MyConstructorThrowsExceptionClass');
    $gClass->setNamespace(__NAMESPACE__);
    $instance = $gClass->newInstance(array(), GClass::WITHOUT_CONSTRUCTOR);
    
    $this->assertInstanceOf($gClass->getFQN(), $instance);
    $this->assertTrue($instance->checkProperty);
  }

  public function testNewClassInstance() {
    $exception = GClass::newClassInstance('Psc\Exception', array('just a test error'));
    $this->assertInstanceOf('Psc\Exception', $exception);
    $this->assertEquals('just a test error', $exception->getMessage());

    $exception = GClass::newClassInstance($gClass = new GClass('Psc\Exception'), array('just a test error'));
    $this->assertInstanceOf('Psc\Exception', $exception);
    $this->assertEquals('just a test error', $exception->getMessage());

    $exception = GClass::newClassInstance($gClass->getReflection(), array('just a test error'));
    $this->assertInstanceOf('Psc\Exception', $exception);
    $this->assertEquals('just a test error', $exception->getMessage());
  }
  
}

class MyConstructorThrowsExceptionClass {
  
  public $checkProperty = TRUE;
  
  public function __construct() {
    throw new \Psc\Exception('this should not be called');
  }
}
?>