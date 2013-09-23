<?php

namespace Webforge\Setup\Installer;

use Webforge\Framework\Package\Package;

class CreateBootstrapPartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\CreateBootstrapPart';
    parent::setUp();

    // because we use dirty hacks in the installerpartTestCase to relocate the "local package" the psc-cms-boot package needed, is not avaible
    // we copy this from the original source
    $this->webforge->getDirectory(Package::VENDOR)->sub('pscheit/psc-cms-boot/')
      ->copy($this->package->getDirectory(Package::VENDOR)->sub('pscheit/psc-cms-boot/')->create(), array('php', 'json'), NULL, $subdirs = TRUE);

    $this->part = new CreateBootstrapPart();
  }
  
  public function testPartCreatesTheBootstrapPHPFile() {
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(array('/bootstrap.php', '/lib/package.boot.php'), $this->getCopiedFiles($this->macro));
  }
}
