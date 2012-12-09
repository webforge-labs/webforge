<?php

namespace Webforge\Setup\Installer;

class InitConfigurationPartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\CreateBootstrapPart';
    parent::setUp();

    $this->part = new InitConfigurationPart();
  }
  
  public function testPartWritesConfigurationAndChangelog() {
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(array('/etc/changelog.php','/etc/config.php'), $this->getWrittenFiles($this->macro));
  }
}
?>