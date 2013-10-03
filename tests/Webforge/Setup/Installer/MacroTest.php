<?php

namespace Webforge\Setup\Installer;

class MacroTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\Macro';
    parent::setUp();

    $this->output = $this->getMockbuilder('Webforge\Common\CommandOutput')->getMockForAbstractClass();
    
    // WTH! phpunit: i have to use getMock here allthough the class is abstract but completely implemented?
    $this->commandA = $this->getMock('Webforge\Setup\Installer\Command');
    $this->commandB = $this->getMock('Webforge\Setup\Installer\Command');
    $this->commandC = $this->getMock('Webforge\Setup\Installer\Command');

    $this->macro = new Macro(array($this->commandA, $this->commandB));
  }
  
  public function testAddACommandAddsACommandToTheMacro() {
    $this->macro->addCommand($this->commandC);
    
    $this->assertEquals(array($this->commandA, $this->commandB, $this->commandC), $this->macro->getCommands());
  }
  
  public function testExecuteExecutesAllCommands() {
    $this->commandA->expects($this->once())->method('execute');
    $this->commandB->expects($this->once())->method('execute');

    $this->macro->execute($this->output);
  }
}
