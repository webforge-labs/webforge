<?php

namespace Webforge\Code\Generator;

class ImportsTest extends \Webforge\Code\Test\Base {
  
  protected $imports;
  
  public function setUp() {
    $this->imports = new Imports(
      Array(
        GClass::create('Doctrine\ORM\Mapping'),
        'DoctrineHelper'=>GClass::create('Psc\Doctrine\Helper')
      )
    );
  }
  
  public function testConstructWithElementsAddsElements() {
    $this->assertCount(2, $this->imports);
    
    $aliases = array();
    foreach ($this->imports as $alias=>$import) {
      $aliases[] = $alias;
      $this->assertInstanceOf('Webforge\Code\Generator\GClass', $import, 'Alias '.$alias.' ist keine GClass');
    }
    
    $this->assertEquals(array('Mapping', 'DoctrineHelper'), $aliases, 'Aliases do not match');
  }
  
  /**
   * @expectedException Psc\Exception
   * @expectedExceptionMessage Alias: DoctrineHelper is already used by Class Psc\Doctrine\Helper
   */
  public function testAddingAnExistingsAliasIsNotAllowed() {
    
    $this->imports->add(GClass::create(get_class($this)), 'DoctrineHelper');
  }
  
  public function testAddingAnExistingsAliasIsNotAllowedAndIsCaseInsensitiv() {
    $this->setExpectedException('Psc\Exception');
    
    $this->imports->add(GClass::create(get_class($this)), 'doctrinehelper');
  }
  
  /**
   * @depends testAddingAnExistingsAliasIsNotAllowed
   */
  public function testAddingAnExistingsAliasCanBeRemoved() {
    $this->imports->remove('DoctrineHelper');
    $this->imports->add(GClass::create(get_class($this)), 'DoctrineHelper');
  }
}
?>