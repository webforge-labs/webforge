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
      $c1 = $this->food->toHTMLList(),
      $c2 = $this->nestedSetConverter->toHTMLList($this->food->toArray())
    );
  }
  
  public function testConversionFromParentPointerToNestedSetFlatArray() {
    $this->assertEquals(
      $c1 = $this->food->toArray(),
      $c2 = $this->nestedSetConverter->fromParentPointer($this->food->toParentPointerArray())
    );
  }
  
  public function testConversionFromParentPointerWithSingleRoot() {
    $this->assertEquals(
      Array ( array('title'=>'root', 'lft'=>1, 'rgt' =>2)),
      $this->nestedSetConverter->fromParentPointer(Array( array('title'=>'root', 'parent'=>NULL) ))
    );
  }
}
?>