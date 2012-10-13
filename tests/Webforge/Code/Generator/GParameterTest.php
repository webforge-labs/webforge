<?php

namespace Webforge\Code\Generator;

class GParameterTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->classParam = GParameter::create('coordinates', $this->classHint = new GClass('Webforge\Data\Models\Coordinates'));
    $this->unknownParam = new GParameter('unknown');
    $this->defParam = new GParameter('enume', $this->createType('String'), 'def');
  }
  
  public function testGparameterDefaultTypeIsMixed() {
    $this->assertEquals('Mixed', $this->unknownParam->getType()->getName());
  }
  
  public function testReturnsHintAsFQNForClassIfTypeIsClass() {
    $this->assertEquals($this->classHint->getFQN(), $this->classParam->getHint());
  }

  public function testReturnsHintAsOnlyClassNameForClassIfTypeIsClass() {
    $this->assertEquals($this->classHint->getName(), $this->classParam->getHint('Webforge\Data\Models'));
  }
  
  public function testReturnsHintOnlyIfTypeCanBeHinted() {
    $this->assertNull($this->unknownParam->getHint());
  }
  
  public function testHintCanBeStringArrayForCreate() {
    $arrayParam = GParameter::create('coordinates', 'array');
    $this->assertInstanceOf('Psc\Data\Type\ArrayType', $arrayParam->getType());
    $this->assertEquals('array', mb_strtolower($arrayParam->getHint()));
  }
  
  public function testParamIsOptionalIfItHasADefaultValue() {
    $this->assertTrue($this->defParam->isOptional());
  }

  public function testHasDefaultValueIsTrue_IfItHasADefaultValue() {
    $param = new GParameter('meanDefault', $this->createType('String'), NULL);
    $this->assertTrue($param->hasDefault());
    $this->assertTrue($param->isOptional());
    
    $param->setDefault(GParameter::UNDEFINED);
    $this->assertFalse($param->hasDefault(), 'hasDefault is not false after setting to undefined ');
    $this->assertFalse($param->isOptional(), 'isOptional is not false after setting to undefined');
  }

  public function testItCanBeTestedIfTheTypeIsExplicitOrNot() {
    $this->assertTrue($this->classParam->hasExplicitType());
    $this->assertFalse($this->unknownParam->hasExplicitType());
  }
}
?>