<?php

namespace Webforge\Setup\Installer;

class InitDoctrinePartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\InitDoctrinePart';
    parent::setUp();
    
    $this->part = new InitDoctrinePart();
  }
  
  public function testCLIPartCopiesBatPHPAndCommandsTemplate() {
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(
      array('/resources/db/001_create_dbs.sql'),
      $this->getWrittenFiles($this->macro)
    );

    $this->assertCount(3, $this->getAddedCLICommands($this->macro));
  }
}
