<?php

namespace Webforge\Code\Generator;

class GClassTest extends \Webforge\Code\Test\Base {

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
}
?>