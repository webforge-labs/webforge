<?php

namespace Webforge\Code\Generator;

/**
 */
class GClassPropertiesTest extends \Webforge\Code\Test\Base {
  
  protected $gClass;
  
  public function setUp() {
    $this->xProperty = new GProperty('x');
    $this->yProperty = new GProperty('y');

    $this->gClass = new GClass('Geometric\Point');
    $this->gClass->addProperty($this->yProperty);
  }
  
  public function testAfterAddingGClassHasAProperty() {
    $this->gClass->addProperty($this->xProperty);
    
    $this->assertTrue($this->gClass->hasProperty($this->xProperty));
    $this->assertTrue($this->gClass->hasProperty('x'));
  }
  
  public function testAfterRemovingAnPropertyTheClassHasThePropertyAnymore() {
    $this->gClass->removeProperty('y');
    $this->assertFalse($this->gClass->hasProperty('y'));
    $this->assertFalse($this->gClass->hasProperty($this->yProperty));
    
    $this->gClass->addProperty($this->xProperty);
    $this->gClass->removeProperty($this->xProperty);
    $this->assertFalse($this->gClass->hasProperty($this->xProperty));
  }
  
  public function testGetProperties() {
    $this->assertCount(1, $this->gClass->getProperties());
    $this->gClass->addProperty($this->xProperty, 0);
    $this->assertCount(2, $this->gClass->getProperties());
    
    $this->assertContainsOnlyInstancesOf('Webforge\Code\Generator\GProperty', $this->gClass->getProperties());
    $this->assertEquals(array('x','y'), $this->reduceCollection($this->gClass->getProperties(), 'name'));
  }
  
  public function testSetProperties() {
    $this->gClass->setProperties(array($this->xProperty, $this->yProperty));
    $this->assertCount(2, $this->gClass->getProperties());
    $this->assertContainsOnlyInstancesOf('Webforge\Code\Generator\GProperty', $this->gClass->getProperties());
  }
}
?>