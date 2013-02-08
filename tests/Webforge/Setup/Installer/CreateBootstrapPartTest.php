<?php

namespace Webforge\Setup\Installer;

class CreateBootstrapPartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\CreateBootstrapPart';
    parent::setUp();

    $this->part = new CreateBootstrapPart();
  }
  
  public function testPartCreatesTheBootstrapPHPFile() {
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(array('/bootstrap.php'), $this->getCopiedFiles($this->macro));
  }
}
?>