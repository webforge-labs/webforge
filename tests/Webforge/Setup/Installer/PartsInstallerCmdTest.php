<?php

namespace Webforge\Setup\Installer;

use Psc\System\Dir;
use Psc\System\File;
use Webforge\Code\Test\ConsoleOutput;

class PartsInstallerCmdTest extends \Webforge\Code\Test\Base {
  
  protected $testDir;
  
  public function setUp() {
    $this->testDir = $this->getMock('Psc\System\Dir');
    $this->container = new \Webforge\Framework\Container();
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS(__DIR__));
    
    $this->output = new ConsoleOutput();
    $this->partsInstaller = new PartsInstaller(array(), $this->container, $this->output);
    
    $this->mockPart = $this->getMockForAbstractClass('Part', array('MockPart'));
  }
  
  public function testCopyCommandCreation() {
    $configFile = $this->onInstallCopyConfigFile();
    
    list($copy) = $this->dryInstall()->getCommands();
    
    $this->assertCmd('Copy', $copy);
    $this->assertEquals($configFile, $copy->getSource());
    $this->assertEquals($this->testDir->sub('etc/'), $copy->getDestination());
  }
  
  public function testExecuteCommandCreation() {    
    $this->onInstall(function ($target, $installer) {
      $installer->execute('/usr/local/bin/somecommand with arguments');
    });
    
    list($exec) = $this->dryInstall()->getCommands();
    
    $this->assertCmd('Exec', $exec);
    $this->assertContains('/usr/local/bin/somecommand with arguments', $exec->describe());
  }

  public function testWriteCommandCreation() {
    $toFile = $this->getMock('Psc\System\File', array(), array('config.php'));
    
    $this->onInstall(function ($target, $installer) use($toFile) {
      $installer->write('some content', $toFile);
    });
    
    list($write) = $this->dryInstall()->getCommands();
    
    $this->assertCmd('Write', $write);
    $this->assertEquals('some content', $write->getContents());
    $this->assertSame($toFile, $write->getDestination());
  }

  public function testWriteTemplateCommandCreation() {
    $tpl = $this->getMock('Psc\System\File', array(), array('config.template.php'));
    $toFile = $this->getMock('Psc\System\File', array(), array('config.php'));
    
    $this->onInstall(function ($target, $installer) use($tpl, $toFile) {
      $installer->writeTemplate($tpl, $toFile);
    });
    
    list($write) = $this->dryInstall()->getCommands();
    
    $this->assertCmd('WriteTemplate', $write);
    $this->assertEquals($tpl, $write->getTemplate());
    $this->assertSame($toFile, $write->getDestination());
  }
  
  public function testGetInstallTemplatesIsADir() {
    $this->assertInstanceOf('Psc\System\Dir', $this->partsInstaller->getInstallTemplates());
  }

  public function testGetWebforgeResourcesIsADir() {
    $this->assertInstanceOf('Psc\System\Dir', $this->partsInstaller->getWebforgeResources());
  }

  protected function dryInstall() {
    return $this->partsInstaller->dryInstall($this->mockPart, $this->testDir);
  }

  protected function onInstall(\Closure $doInstall) {
    $this->mockPart->expects($this->once())->method('installTo')->will($this->returnCallback($doInstall));
  }

  protected function onInstallCopyConfigFile() {
    $configFile = $this->getMock('Psc\System\File', array(), array('config.template.php'));
    
    $this->onInstall(function (Dir $target, Installer $installer) use ($configFile) {
      $installer->copy($configFile, $target->sub('etc/'));
    });
    
    return $configFile;
  }
  
  protected function assertCmd($name, $command) {
    $this->assertInstanceOf('Webforge\Setup\Installer\\'.$name.'Cmd', $command);
  }
}
?>