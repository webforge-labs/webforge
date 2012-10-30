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

    $this->evm = $this->getMockBuilder('Doctrine\Common\EventManager')
                  ->disableOriginalConstructor()
                  ->getMock();
    
    $this->food = new \Webforge\TestData\NestedSet\FoodCategories();

    $this->bridge = new DoctrineBridge($this->em);
  }
  
  public function testBridgeIsSubscribingToEntityManager() {
    $this->assertInstanceof('Doctrine\Common\EventSubscriber', $this->bridge);

    $this->expectBridgeSubscribesToEVMinEM();
    $this->bridge->startTransaction();
  }
  
  protected function expectBridgeSubscribesToEVMinEM() {
    $this->evm->expects($this->once())->method('addEventSubscriber')->with($this->identicalTo($this->bridge));
    $this->em->expects($this->once())->method('getEventManager')->will($this->returnValue($this->evm));
  }
  
  public function testGathersEntitiesInTransactionsForConverter() {
    $this->expectBridgeSubscribesToEVMinEM();
    
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
    
    
    $this->bridge->startTransaction();
    
    // this is what prePersist() would do (for sake of simplicity, and: we don't want to mock the behaviour from doctrine here)
    $this->bridge->addNode($node1); 
    $this->bridge->addNode($node2); 
    $this->bridge->addNode($node3);
    
    $this->bridge->commit();
  }
}
?>
