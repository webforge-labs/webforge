<?php

namespace Webforge\Setup\Installer;

class WriteHtaccessPartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    parent::setUp();

    $this->part = new WriteHtaccessPart();
  }
  
  public function testPartWritesConfigurationAndChangelog() {
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(array('/www/.htaccess'), $this->getCopiedFiles($this->macro));
  }
}
?>