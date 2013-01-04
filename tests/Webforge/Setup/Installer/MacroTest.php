<?php

namespace Webforge\Setup\Installer;

class MacroTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\Macro';
    parent::setUp();
    
    $this->commandA = $this->getMockForAbstractClass('Webforge\Setup\Installer\Command');
    $this->commandB = $this->getMockForAbstractClass('Webforge\Setup\Installer\Command');
    $this->commandC = $this->getMockForAbstractClass('Webforge\Setup\Installer\Command');
    
    $this->macro = new Macro(array($this->commandA, $this->commandB));
  }
  
  public function testAddACommandAddsACommandToTheMacro() {
    $this->macro->addCommand($this->commandC);
    
    $this->assertEquals(array($this->commandA, $this->commandB, $this->commandC), $this->macro->getCommands());
  }
  
  public function testExecuteExecutesAllCommands() {
    $this->commandA->expects($this->once())->method('execute');
    $this->commandB->expects($this->once())->method('execute');
    
    $this->macro->execute();
  }
}
?>