<?php

namespace Webforge\Setup\Installer;

class ApacheConfigurationPartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\ApacheConfigurationPart';
    parent::setUp();
    
    $this->part = new ApacheConfigurationPart();
  }
  
  public function testPartCreatesTheApacheConfigurationConfInEtc() {
    $macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(
      array('/etc/apache2/psc.conf'),
      $this->getWrittenFiles($macro)
    );
  }
}
?>