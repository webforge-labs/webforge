<?php

namespace Webforge\Setup\Installer;

class CreateCLIPartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\CreateCLIPart';
    parent::setUp();
    
    $this->part = new CreateCLIPart();
  }
  
  public function testCLIPartCopiesBatPHPAndCommandsTemplate() {
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $createdFiles = array();
    foreach ($this->findCopyCmds($this->macro) as $copy) {
      $createdFiles[] = $copy->getDestination()->getUrl($this->target);
      
      $this->assertFileExists($copy->getSource(), 'source does not exist: '. $copy->describe());
    }

    $this->assertArrayEquals(
      array('/bin/cli.bat','/bin/cli.php','/lib/inc.commands.php'),
      $createdFiles
    );
  }
}
?>