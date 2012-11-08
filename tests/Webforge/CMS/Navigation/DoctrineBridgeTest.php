<?php

namespace Webforge\CMS\Navigation;

/**
 */
class DoctrineBridgeTest extends \Webforge\Code\Test\Base {
  
  protected $em, $evm;
  protected $food;
  
  public function setUp() {
    parent::setUp();
    
    $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                  ->disableOriginalConstructor()
                  ->getMock();

    $this->food = new \Webforge\TestData\NestedSet\FoodCategories();
    $this->bridge = new DoctrineBridge($this->em);
  }
  
  public function testGathersEntitiesInTransactionsForConverter() {
    list ($node1, $node2, $node3) = array_map(
      function ($arrayNode) {
        return new SimpleNode($arrayNode);
      },
      array_slice($this->food->toParentPointerArray(), 0, 3)
    );
    
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
}
?>
