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
    
    $this->expectNestedSetConverterFromParentPointer();
    
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
    $this->bridge->beginTransaction();
    list ($node1, $node2, $node3) = $this->nodes;

    $this->bridge->persist($node1); 
    $this->bridge->persist($node2); 
    $this->bridge->persist($node3);
    
    $this->assertEquals(array($node1, $node2, $node3), $this->bridge->getNodes());
  }

  public function testCallsOnCommitCommandForRootNodeIfSpecified() {
    $that = $this;
    $called = FALSE;

    $this->bridge->onCommitRootNode(function ($rootNode) use ($that, &$called) {
      $that->assertEquals(1, $rootNode->getLft(),'rootNode should have lft = 1');
      $called = TRUE;
    });
    
    $this->expectNestedSetConverterFromParentPointer();
    $this->bridge->beginTransaction();

    list ($node1, $node2, $node3) = $this->nodes;
    $this->bridge->persist($node1); 
    $this->bridge->persist($node2); 
    $this->bridge->persist($node3);

    $this->assertFalse($called);
    $this->bridge->commit();
    $this->assertTrue($called, 'onCommitRootNode-Hook is not called');
  }

  protected function expectNestedSetConverterFromParentPointer() {
    list ($node1, $node2, $node3) = $this->nodes;
    $converter = $this->getMock('NestedSetConverter');
    $converter
      ->expects($this->once())->method('fromParentPointer')
      ->with($this->equalTo(
        array($node1, $node2, $node3)
      ))
      ->will($this->returnCallback(function ($nodes) {
        $nodes[0]->setLft(1)->setRgt(2); // fake content converter
        $nodes[1]->setLft(3)->setRgt(4);
        $nodes[2]->setLft(5)->setRgt(6);
      }));
    $this->bridge->setConverter($converter);

  }
}
?>
