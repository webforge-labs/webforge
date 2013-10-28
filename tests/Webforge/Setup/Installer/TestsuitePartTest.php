<?php

namespace Webforge\Setup\Installer;

class TestSuitePartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\TestSuitePart';
    parent::setUp();

    $this->part = new TestSuitePart();
  }
  
  public function testPartWritesConfigurationAndChangelog() {
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(array('/phpunit.xml.dist'), $this->getWrittenFiles($this->macro));
  }
}
