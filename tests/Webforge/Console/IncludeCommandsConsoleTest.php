<?php

namespace Webforge\Console;

use Webforge\Framework\Container as WebforgeContainer;

class IncludeCommandsConsoleTest extends \Webforge\Code\Test\Base {
  
  protected $console;
  protected $application;
  
  protected $commandsFile;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Console\\IncludeCommandsConsole';
    parent::setUp();
    
    $this->container = new WebforgeContainer();
    $this->commandsFile = $this->container->getResourceDirectory()->getFile('installTemplates/inc.commands.template.php');
    
    $this->application = $this->getMockBuilder('Symfony\Component\Console\Application')->disableOriginalConstructor()->getMock();
    
    $this->console = new IncludeCommandsConsole($this->application);
  }
  
  public function testNameSetsNameOfApplication() {
    $name = 'SomeNiceCLIApp';
    $this->application->expects($this->once())->method('setName')->with($name);
    
    $this->console->setName($name);
  }
  
  public function testVersionSetsVersionOfApplication() {
    $version = '1.0';
    $this->application->expects($this->once())->method('setVersion')->with($version);
    
    $this->console->setVersion($version);
  }
  
  public function testIncludeCommandIncludesCommandsFromFile() {
    $this->application->expects($this->once())->method('addCommands');
    
    $this->console->includeCommandsFromFile($this->commandsFile);
  }
}
?>