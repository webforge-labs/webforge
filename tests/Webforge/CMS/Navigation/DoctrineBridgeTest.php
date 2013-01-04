<?php

namespace Webforge\CMS\Navigation;

/**
 */
class DoctrineBridgeTest extends \Webforge\Code\Test\Base {
  
  protected $em, $evm;
  protected $food;
  
  protected $nodes;
  
  public function setUp() {
    parent::setUp();
    
    $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                  ->disableOriginalConstructor()
                  ->getMock();

    $this->food = new \Webforge\TestData\NestedSet\FoodCategories();
    $this->bridge = new DoctrineBridge($this->em);
    
    $this->nodes = array_map(
      function ($arrayNode) {
        return new SimpleNode($arrayNode);
      },
      array_slice($this->food->toParentPointerArray(), 0, 3)
    );
  }
  
  public function testGathersEntitiesInTransactionsForConverter() {
    list ($node1, $node2, $node3) = $this->nodes;
    
    $converter = $this->getMock('NestedSetConverter');
    $converter
      ->expects($this->once())->method('fromParentPointer')
      ->with($this->equalTo(
        array($node1, $node2, $node3)
      ));
    $this->bridge->setConverter($converter);
    
    $this->bridge->beginTransaction();
    
    $this->bridge->persist($node1); 
    $this->bridge->persist($node2); 
    $this->bridge->persist($node3);
    
    $this->bridge->commit();
  }
  
  public function testBridgeConstructsAConverter() {
    $this->assertInstanceOf('Webforge\CMS\Navigation\NestedSetConverter', $this->bridge->getConverter());
  }
  
  public function testExposesNodesAsArray() {
    list ($node1, $node2, $node3) = $this->nodes;
    
    $this->bridge->beginTransaction();

    $this->bridge->persist($node1); 
    $this->bridge->persist($node2); 
    $this->bridge->persist($node3);
    
    $this->assertEquals(array($node1, $node2, $node3), $this->bridge->getNodes());
  }
}
?>
