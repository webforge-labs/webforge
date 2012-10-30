<?php

namespace Webforge\CMS\Navigation;

/**
 */
class NestedSetConverterTest extends \Psc\Code\Test\Base {
  
  protected $nestedSetConverter, $food;
  
  public function setUp() {
    parent::setUp();
    $this->nestedSetConverter = new NestedSetConverter();
    $this->food = new \Webforge\TestData\NestedSet\FoodCategories();
  }
  
  public function testAndCreateTheArrayStructureSnippet() {
    $this->assertXmlStringEqualsXmlString(
      $this->food->toHTMLList(),
      $this->nestedSetConverter->toHTMLList($this->wrap($this->food->toArray()))
    );
  }
  
  public function testConversionFromParentPointerToNestedSetFlatArray() {
    $this->assertEquals(
      $this->food->toArray(),
      $this->unwrap($this->nestedSetConverter->fromParentPointer($this->wrap($this->food->toParentPointerArray())))
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
      
      $node = new TestNode($arrayNode);
      $nodes[] = $node;
      $nodesByTitle[$node->getTitle()] = $node;
    }
    
    return $nodes;
  }
  
  /**
   * Converts the node of the interface into an array node
   */
  protected function unwrap(Array $objectNodes) {
    return array_map(function (TestNode $node) {
      return $node->unwrap();
    }, $objectNodes);
  }
}

class TestNode implements Node {
  
  protected $lft, $rgt, $root, $depth, $title, $parent;
  
  public function __construct(Array $node) {
    $this->title = $node['title'];
    
    if (isset($node['depth']))
      $this->depth = $node['depth'];
    
    if (isset($node['lft']))
      $this->lft = $node['lft'];
      
    if (isset($node['rgt']))
      $this->rgt = $node['rgt'];
      
    if (isset($node['root'])) {
      $this->root = $node['root'];
    }

    if (isset($node['parent'])) {
      $this->parent = $node['parent'];
    }
  }
  
  public function unwrap() {
    return array (
      'title' => $this->title,
      'rgt' => $this->rgt,
      'lft' => $this->lft,
      'depth' => $this->depth
      //'root' => $this->root
    );
  }
  
  public function getNodeHTML() {
    return '<a>'.$this->title.'</a>';
  }
  
  public function equalsNode(Node $other = NULL) {
    return isset($other) && $other->getTitle() === $this->getTitle();
  }
  
  /**
   * @param TestNode $parent
   * @chainable
   */
  public function setParent(TestNode $parent) {
    $this->parent = $parent;
    return $this;
  }

  /**
   * @return TestNode
   */
  public function getParent() {
    return $this->parent;
  }
  
  /**
   * @param string $title
   * @chainable
   */
  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

  /**
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }


  
  /**
   * @param integer $depth
   * @chainable
   */
  public function setDepth($depth) {
    $this->depth = $depth;
    return $this;
  }

  /**
   * @return integer
   */
  public function getDepth() {
    return $this->depth;
  }

  /**
   * @param integer $root
   * @chainable
   */
  public function setRoot($root) {
    $this->root = $root;
    return $this;
  }

  /**
   * @return integer
   */
  public function getRoot() {
    return $this->root;
  }

  /**
   * @param int $rgt
   * @chainable
   */
  public function setRgt($rgt) {
    $this->rgt = $rgt;
    return $this;
  }

  /**
   * @return int
   */
  public function getRgt() {
    return $this->rgt;
  }

  /**
   * @param int $lft
   * @chainable
   */
  public function setLft($lft) {
    $this->lft = $lft;
    return $this;
  }

  /**
   * @return int
   */
  public function getLft() {
    return $this->lft;
  }
}
?>