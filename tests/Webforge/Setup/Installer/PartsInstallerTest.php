<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Common\System\File;
use Webforge\Code\Test\ConsoleOutput;

class PartsInstallerTest extends \Webforge\Code\Test\Base {
  
  protected $testDir;
  
  public function setUp() {
    $this->testDir = $this->getMock('Webforge\Common\System\Dir');
    $this->container = new \Webforge\Framework\Container();
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS(__DIR__));
    
    $this->output = new ConsoleOutput();
    $this->partsInstaller = new PartsInstaller(array(), $this->container, $this->output);
    
    $this->mockPart = $this->getMockForAbstractClass('Part', array('MockPart'));
  }
  

  public function testPartsInstallerAssignsContainerForPartsThatAreContainerAware() {
    $part = $this->getMockBuilder('Webforge\Setup\Installer\ContainerAwarePart')
                 ->setConstructorArgs(array('containerTestPart'))
                 ->setMethods(array('setContainer', 'installTo'))
                 ->getMock();
    $part->expects($this->once())->method('setContainer')->with($this->isInstanceOf('Webforge\Framework\Container'));
    
    $this->partsInstaller->dryInstall($part, $this->testDir);
  }

  public function testPartsInstallerAssignsPackageofLocalProjectForPartsThatArePackageAware() {
    $part = $this->getMockBuilder('PackageAwareTestPart')
              ->setConstructorArgs(array('packageTestPart'))
              ->setMethods(array('setPackage','getPackage','installTo'))
              ->getMock();
    $part->expects($this->once())->method('setPackage')->with($this->isInstanceOf('Webforge\Setup\Package\Package'));
    
    $this->partsInstaller->dryInstall($part, $this->testDir);
  }

  public function testPartsInstallerTHrowsRuntimeExceptionIfPartWithUnknownNameIsget() {
    $this->setExpectedException('RuntimeException');
    $this->partsInstaller->addPart($this->mockPart);
    
    $this->partsInstaller->getPart('thisisnotinpartsinstaller');
  }
  
  public function testGetPartReturnsAPart() {
    $this->partsInstaller->addPart($this->mockPart);
    
    $this->assertSame($this->mockPart, $this->partsInstaller->getPart('MockPart'));
  }

  public function testPartsInstallerHasAWarningFunctionThatPrintsToOutput() {
    $this->partsInstaller->warn('i would install the bootstrap first');
    $this->assertContains('i would install the bootstrap first', $this->output->stream);
  }
  
  public function testPartsInstallerCreatesAMacroInDry() {
    $macro = $this->partsInstaller->dryInstall($this->mockPart, $this->testDir);
    
    $this->assertInstanceOf('Webforge\Setup\Installer\Macro', $macro);
  }

  public function testPartsInstallerDoesNotCallPartInstallToinDry() {
    $configFile = $this->onInstallCopyConfigFile();
    
    $configFile->expects($this->never())->method('copy');
    
    $this->dryInstall();
  }

  public function testPartsInstallerDoesCallPartInstallToInInstall() {
    $configFile = $this->onInstallCopyConfigFile();
    
    $configFile->expects($this->once())->method('copy');
    
    $this->partsInstaller->install($this->mockPart, $this->testDir);
  }
  
  protected function onInstall(\Closure $doInstall) {
    $this->mockPart->expects($this->once())->method('installTo')->will($this->returnCallback($doInstall));
  }

  protected function onInstallCopyConfigFile() {
    $configFile = $this->getMock('Webforge\Common\System\File', array(), array('config.template.php'));
    
    $this->onInstall(function (Dir $target, Installer $installer) use ($configFile) {
      $installer->copy($configFile, $target->sub('etc/'));
    });
    
    return $configFile;
  }
  
  protected function dryInstall() {
    return $this->partsInstaller->dryInstall($this->mockPart, $this->testDir);
  }
  
  protected function assertCmd($name, $command) {
    $this->assertInstanceOf('Webforge\Setup\Installer\\'.$name.'Cmd', $command);
  }
}

abstract class PackageAwareTestPart extends Part implements \Webforge\Setup\Package\PackageAware {
  
}
?>