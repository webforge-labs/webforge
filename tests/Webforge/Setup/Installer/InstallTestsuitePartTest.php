<?php

namespace Webforge\Setup\Installer;

class InstallTestsuitePartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\InstallTestsuitePart';
    parent::setUp();

    $this->part = new InstallTestsuitePart();
  }
  
  public function testPartWritesConfigurationAndChangelog() {
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(array('/phpunit.xml.dist'), $this->getWrittenFiles($this->macro));
  }
}
?>