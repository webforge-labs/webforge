<?php

namespace Webforge\CMS\Navigation;

/**
 */
class NestedSetConverterTest extends \Psc\Code\Test\Base {
  
  protected $nestedSetConverter, $food;
  
  public function setUp() {
    parent::setUp();
    $this->nestedSetConverter = new NestedSetConverter();
  }
  
  public static function getFixtures() {
    return Array(
      array(new \Webforge\TestData\NestedSet\FoodCategories()),
      array(new \Webforge\TestData\NestedSet\Consumables()),
    );
  }
  
  /**
   * @dataProvider getFixtures
   */
  public function testAndCreateTheArrayStructureSnippet($fixture) {
    $this->assertXmlStringEqualsXmlString(
      $fixture->toHTMLList(),
      $this->nestedSetConverter->toHTMLList($this->wrap($fixture->toArray()))
    );
  }
  
  /**
   * @dataProvider getFixtures
   */
  public function testConversionFromParentPointerToNestedSetFlatArray($fixture) {
    $this->assertEquals(
      $fixture->toArray(),
      $this->unwrap($this->nestedSetConverter->fromParentPointer($this->wrap($fixture->toParentPointerArray())))
    );
  }
  
  public function testConversionFromParentPointerWithSingleRoot() {
    $this->assertEquals(
      Array ( array('title'=>'root', 'lft'=>1, 'rgt' =>2, 'depth'=>0)),
      $this->unwrap($this->nestedSetConverter->fromParentPointer($this->wrap(Array( array('title'=>'root', 'parent'=>NULL, 'depth'=>0)))))
    );
  }
  
  /**
   * Converts the array nodes from the fixture into a node of the interface
   */
  protected function wrap(Array $arrayNodes) {
    $nodes = array();
    $nodesByTitle = array();
    foreach ($arrayNodes as $arrayNode) {
      if (isset($arrayNode['parent'])) {
        $arrayNode['parent'] = $nodesByTitle[ $arrayNode['parent'] ];
      }
      
      $nodes[] = $node = new SimpleNode($arrayNode);
      $nodesByTitle[$node->getTitle()] = $node;
    }
    
    return $nodes;
  }
  
  /**
   * Converts the node of the interface into an array node
   */
  protected function unwrap(Array $objectNodes) {
    return array_map(function (SimpleNode $node) {
      return $node->unwrap();
    }, $objectNodes);
  }
}
?>