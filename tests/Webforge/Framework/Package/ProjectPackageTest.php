<?php

namespace Webforge\Framework\Package;

use Webforge\Configuration\Configuration;
use Webforge\Setup\MissingConfigVariableException;

class ProjectPackageTest extends \Webforge\Framework\Package\PackagesTestCase {

  protected $projectPackage;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\Package\\ProjectPackage';
    parent::setUp();

    $this->projectPackage = new ProjectPackage($this->configPackage, 'ACMESuperBlog', 'super-blog', 0, 'psc');
  }

  public function testCreatedNonFactoryProjectPackagesDoNotHaveAconfiguration() {
    $this->setExpectedException('RuntimeException');
    $this->projectPackage->getConfiguration();
  }

  public function testGetRootDirectoryReturnsPackageRoot() {
    $this->assertSame(
      $this->projectPackage->getRootDirectory(),
      $this->configPackage-> getRootDirectory()
    );
  }

  public function testIsStagingAndsetStaging() {
    $this->assertFalse($this->projectPackage->isStaging());

    $this->projectPackage->setStaging(TRUE);
    $this->assertTrue($this->projectPackage->isStaging());

    $this->projectPackage->setStaging(FALSE);
    $this->assertFalse($this->projectPackage->isStaging());
  }

}
