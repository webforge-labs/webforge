<?php

namespace Webforge\Setup\Installer;

use Webforge\Setup\AutoLoadInfo;

class CreateCLIPartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\CreateCLIPart';
    parent::setUp();
    
    $this->part = new CreateCLIPart();

    $this->container->getLocalPackage()->setAutoLoadInfo(
      new AutoLoadInfo(
        json_decode('{"psr-0": {"PackageNamespace": ["lib/"]}}')
      )
    );
  }
  
  public function testCLIPartCopiesBatPHPAndCommandsTemplate() {
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $createdFiles = array();
    foreach ($this->findCopyCmds($this->macro) as $copy) {
      $createdFiles[] = $copy->getDestination()->getUrl($this->target);
      
      $this->assertFileExists($copy->getSource(), 'source does not exist: '. $copy->describe());
    }

    $this->assertArrayEquals(
      array('/bin/cli.bat','/bin/cli.php','/bin/cli.sh', '/lib/inc.commands.php'),
      $createdFiles
    );
  }
  
  public function testCLIPartMakesAHintToInstallPscCMSConsole() {
    $this->output->expects($this->once())->method('msg')->with(
      $this->logicalAnd(
        $this->stringContains('create-class'),
        $this->stringContains('PackageNamespace\CMS\ProjectConsole'), // class
        $this->stringContains('Psc\CMS\ProjectConsole') // extends
      )
    );
    
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
  }
}
