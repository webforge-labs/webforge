<?php

namespace Webforge\Code\Test;

use Webforge\Setup\Installer\PartsInstaller;
use Webforge\Setup\Installer\Macro;
use Webforge\Setup\Installer\Command;
use Webforge\Framework\Container;
use Psc\System\Dir;

class InstallerPartTestCase extends MacroTestCase {
  
  protected $installer;
  protected $container;
  
  public function setUp() {
    parent::setUp();
    
    $this->target = Dir::createTemporary();
    $this->container = new Container();
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS(__DIR__));
    
    $this->installer = new PartsInstaller(array(), $this->container);
  }
  
  public function findCopyCmds(Macro $macro) {
    $copies = array();
    foreach ($macro->getCommands() as $command) {
      if ($this->isCmd('Copy', $command)) {
        $copies[] = $command;
      }
    }
    return $copies;
  }

  public function findWriteOrWriteTemplateCmds(Macro $macro) {
    $commands = array();
    foreach ($macro->getCommands() as $command) {
      if ($this->isCmd('Write', $command) || $this->isCmd('WriteTemplate', $command)) {
        $commands[] = $command;
      }
    }
    return $commands;
  }
  
  public function getCopiedFiles($macro) {
    $copiedFiles = array();
    foreach ($this->findCopyCmds($macro) as $copy) {
      $copiedFiles[(string) $copy->getSource()] = $copy->getDestination()->getUrl($this->target);
      
      $this->assertFileExists($copy->getSource(), 'source does not exist: '. $copy->describe());
    }
    
    return $copiedFiles;
  }

  public function getWrittenFiles($macro) {
    $files = array();
    foreach ($macro->getCommands() as $write) {
      if ($this->isCmd('WriteTemplate', $write)) {
        $files[(string) $write->getTemplate()] = $write->getDestination()->getUrl($this->target);
        $this->assertFileExists($write->getTemplate(), 'template does not exist: '. $write->describe());
      } elseif($this->isCmd('Write', $write)) {
        $files[] = $write->getDestination()->getUrl($this->target);
      }
    }
    
    return $files;
  }
  
  public function isCmd($name, Command $command) {
    $class = 'Webforge\Setup\Installer\\'.$name.'Cmd';
    return $command instanceof $class;
  }
  
  public function debugMacro(Macro $macro) {
    $x = 0;
    $str = '';
    foreach ($macro->getCommands() as $command) {
      $str .= sprintf("[%s] %s\n", $x, $command->describe());
      $x++;
    }
    
    return $str;
  }
  
  public function tearDown() {
    if (isset($this->target)) {
      $this->target->delete();
    }
  }
}
?>