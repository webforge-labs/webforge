<?php

namespace Webforge\Setup\Installer;

class PscJSBoilerplatePartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    parent::setUp();

    $this->part = new PscJSBoilerplatePart();
  }
  
  public function testPartWritesConfigurationAndChangelog() {
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(array(
        '/www/js/main.js',
        '/www/js/boot.js',
        '/www/js/config.js',
        '/www/js/html5-ie-fix.js'
      ),
      $this->getCopiedFiles($this->macro),
      $this->debugMacro($this->macro)
    );
  }
}
?>