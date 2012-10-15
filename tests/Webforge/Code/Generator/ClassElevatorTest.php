<?php

namespace Webforge\Code\Generator;

use Webforge\Code\GlobalClassFileMapper;
use Psc\System\File;

class ClassElevatorTest extends \Webforge\Code\Test\Base {

  public function setUp() {
    $this->classReader = $this->getMockBuilder('ClassReader')->disableArgumentCloning()->getMock();
    
    $container = new \Webforge\Framework\Container();
    
    $this->elevator = new ClassElevator(
      $container->getClassFileMapper(),
      $this->classReader
    );
    
    $this->gClass = new GClass(get_class($this));
  }
  
  public function testThatElevatorReadsTheClassFromSource() {
    $this->classReader->expects($this->once())->method('readInto')
                      ->will($this->returnValue($this->gClass));
                      
    $this->assertNotSame($this->gClass, $this->elevator->getGClass(get_class($this)));
  }
  
  public function testParentElevation() {
    $child = new GClass('Webforge\Geometric\Point');
    $parent = new GClass('Webforge\Geometric\Base');
    $child->setParent($parent);
    
    $this->classReader->expects($this->once())->method('readInto')
                      ->with($this->isInstanceOf('Psc\System\File'), $this->identicalTo($parent))
                      ->will($this->returnValue($parent));
    
    $child = $this->elevator->elevateParent($child);
    
    $this->assertSame($parent, $child->getParent());
  }

  public function testInterfaceElevation() {
    $gClass = new GClass('Webforge\Geometric\Point');
    $exportable = new GClass('Webforge\Common\Exportable');
    $gClass->addInterface($exportable);
    
    $this->classReader->expects($this->once())->method('readInto')
                      ->will($this->returnCallback(
                              function ($file, $exportable) {
                                $exportable->createMethod('export');
                              }
                            ));
    
    $gClass = $this->elevator->elevateInterfaces($gClass);
    
    $this->assertSame($exportable, $gClass->getInterface(0));
    //$this->assertThatGClass($exportable)->hasMethod('export');
  }
}
?>