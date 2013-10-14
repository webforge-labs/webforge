<?php

namespace Webforge\Framework\Package;

use Webforge\Configuration\Configuration;
use Webforge\Setup\MissingConfigVariableException;
use Webforge\Common\System\Dir;

class ProjectPackageTest extends \Webforge\Framework\Package\PackagesTestCase {

  protected $projectPackage;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\Package\\ProjectPackage';
    parent::setUp();

    $hostConfig = new Configuration(array());
    $this->projectPackage = new ProjectPackage($this->configPackage, 'ACMESuperBlog', 'super-blog', 0, 'psc', new ProjectUrls($hostConfig));
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

  public function testSetRootDirectory() {
    $this->projectPackage->setRootDirectory($dir = Dir::factoryTS(__DIR__));

    $this->assertEquals($dir, $this->projectPackage->dir('root'));
    $this->assertEquals($dir, $this->projectPackage->getRootDirectory());
  }

  public function testCloneIsNotSensibleToRootDirectoryChanges() {
    $clonedPackage = clone $this->projectPackage;

    $this->projectPackage->setRootDirectory($dir = Dir::factoryTS(__DIR__));

    $this->assertEquals((string) $dir, (string) $this->projectPackage->getRootDirectory());
    
    $this->assertNotEquals((string) $dir, (string) $clonedPackage->getRootDirectory(), 'cloned directory should not change!');
    $this->assertNotEquals((string) $dir, (string) $clonedPackage->dir('root'), 'cloned directory root retrieved with dir() should not change!');
  }
}
