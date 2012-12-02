<?php

namespace Webforge\Setup\Installer;

use Psc\System\Dir;
use Psc\System\File;

class PartsInstallerTest extends \Webforge\Code\Test\Base {
  
  protected $testDir;
  
  public function setUp() {
    $this->testDir = Dir::createTemporary();
    $this->container = new \Webforge\Framework\Container();
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS(__DIR__));
    $this->partsInstaller = new PartsInstaller(array(), $this->container);
    
    $this->existingFile = File::createTemporary();
    $this->existingFile->writeContents('.');
  }
  
  public function testPartsInstallerCopiesTheTemplatesOfThePartToADefinedTargetDirectory() {
    $template = File::createTemporary();
    $template->writeContents('<?php echo "testing"; ?>');
    
    $part = new TestPart($template);
    $this->partsInstaller->install($part, $this->testDir);
    
    $this->assertEquals($this->testDir->getFile('testing.php')->getContents(), '<?php echo "testing"; ?>');
    $template->delete();
  }
  
  public function testPartsInstallerAssignsContainerForPartsThatAreContainerAware() {
    $part = $this->getMockForAbstractClass('ContainerAwareTestPart', array('containerTestPart'));
    $part->expects($this->once())->method('setContainer')->with($this->isInstanceOf('Webforge\Framework\Container'));
    
    $this->partsInstaller->install($part, $this->testDir);
  }

  public function testPartsInstallerAssignsPackageofLocalProjectForPartsThatArePackageAware() {
    $part = $this->getMockForAbstractClass('PackageAwareTestPart', array('packageTestPart'));
    $part->expects($this->once())->method('setPackage')->with($this->isInstanceOf('Webforge\Setup\Package\Package'));
    
    $this->partsInstaller->install($part, $this->testDir);
  }
  
  public function testPartsInstallerTHrowsRuntimeExceptionIfPartWithUnknownNameIsget() {
    $this->setExpectedException('RuntimeException');
    $this->partsInstaller->getPart('thisisnotinpartsinstaller');
  }
  
  public function testPartsInstallerCopiesFilesOnlyIfTheyNotExistsWhenFlagIsSet() {
    $source = $this->getMock('Psc\System\File', array(), array(__FILE__));
    $source->expects($this->never())->method('copy');
    
    $this->partsInstaller->copy($source, $target = $this->existingFile, Installer::IF_NOT_EXISTS);
  }
  
/*
 *  public function testPartsInstallerVerbosesCopyActionToOutput() {
    $this->partsInstaller->copy($source, $target)
  }
*/
  
  public function testPartsInstallerHasAWarningFunction() {
    $this->partsInstaller->warn('i would install the bootstrap first');
  }
  
  public function testExecuteFunctionDelegatesACommandTotheSystem() {
    //$this->partsInstaller->warn('i would install the bootstrap first');
    $this->markTestIncomplete();
  }

  
  public function tearDown() {
    $this->testDir->delete();
    $this->existingFile->delete();
  }
}

abstract class ContainerAwareTestPart extends Part implements \Webforge\Framework\ContainerAware {
  
}

abstract class PackageAwareTestPart extends Part implements \Webforge\Setup\Package\PackageAware {
  
}
?>