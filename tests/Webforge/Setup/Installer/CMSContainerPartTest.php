<?php

namespace Webforge\Setup\Installer;

class CMSContainerPartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\CMSContainerPart';
    parent::setUp();
    
    $this->part = new CMSContainerPart();
  }
  
  public function testCopiesExpectedFiles() {
    $this->expectQuestion(
      $this->stringContains('ControllerNamespace'),
      'Webforge\My\Controllers'
    );

    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(
      array('/lib/'.$this->package->getNamespace().'/CMS/Container.php'),
      $this->getWrittenFiles($this->macro)
    );
  }
}
