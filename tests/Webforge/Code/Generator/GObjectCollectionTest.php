<?php

namespace Webforge\Code\Generator;

class GObjectCollectionTest extends \Webforge\Code\Test\Base {
  
  protected $c;
  protected $o1, $o2, $o3;
  
  public function setUp() {
    $this->o1 = new TestObject('object 1');
    $this->o2 = new TestObject('object 2');
    $this->o3 = new TestObject('object 3');
    
    $this->c = new GObjectCollection(array($this->o1));
  }
  
  public function testAddAddsToCollection() {
    $this->c->add($this->o2);
    $this->c->add($this->o3);
    
    $this->assertContent(array($this->o1, $this->o2, $this->o3));
  }

  public function testAddAddsToCollectionWithoutParameterAlwaysAtTheEnd() {
    $this->c->add($this->o3);
    $this->c->add($this->o2, GObjectCollection::END);
    
    $this->assertContent(array($this->o1, $this->o3, $this->o2));
  }

  public function testAddAddsToCollectionWithNumericPosition() {
    $this->c->add($this->o3);
    $this->c->add($this->o2, 1);
    
    $this->assertContent(array($this->o1, $this->o2, $this->o3));
  }
  
  public function testGFromCollectionByKey() {
    $this->assertSame($this->o1, $this->c->get('object 1'));
  }

  public function testGetFromCollectionByIndex() {
    $this->c->add($this->o2);
    $this->c->add($this->o3);

    $this->assertSame($this->o1, $this->c->get(0));
    $this->assertSame($this->o3, $this->c->get(2));
  }
  
  public function testHasWithObject() {
    $this->assertTrue($this->c->has($this->o1));
    $this->assertFalse($this->c->has($this->o2));
  }

  public function testHasWithKey() {
    $this->assertTrue($this->c->has('object 1'));
    $this->assertFalse($this->c->has('object 2'));
  }
  
  public function testRemoveFromCollection() {
    $this->c->remove($this->o1);
    
    $this->assertContent(array());
  }
  
  public function testSetOrderWithObject() {
    $this->c->add($this->o2);
    $this->c->add($this->o3);
    
    xdebug_break();
    $this->c->setOrder($this->o3, 0);
    $this->assertContent(array($this->o3, $this->o1, $this->o2));
  }
  
  protected function assertContent(Array $array) {
    $collection = $this->c->toArray();
    $this->assertContainsOnlyInstancesOf('Webforge\Code\Generator\TestObject', $collection);
    
    $this->assertEquals(
      $this->reduceCollection($array, 'key'),
      $this->reduceCollection($collection, 'key')
    );
  }
}

class TestObject extends GObject {
  
  public $name;
  
  public function __construct($name) {
    $this->name = $name;
  }
  
  public function getKey() {
    return $this->name;
  }
}
?>