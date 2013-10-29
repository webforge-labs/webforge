<?php

namespace Webforge\Framework\Package;

use Webforge\Configuration\Configuration;
use Webforge\Setup\MissingConfigVariableException;
use Webforge\Common\System\Dir;
use Webforge\Framework\DirectoryLocations;

class ProjectPackageTest extends \Webforge\Framework\Package\PackagesTestCase {

  protected $projectPackage;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\Package\\ProjectPackage';
    parent::setUp();

    $hostConfig = new Configuration(array());
    $this->projectPackage = new ProjectPackage(
      $this->configPackage, 
      'ACMESuperBlog', 
      'super-blog', 
      $mode = 0, 
      $host = 'psc', 
      new ProjectUrls($hostConfig), 
      new DirectoryLocations($this->configPackage->getRootDirectory(), array())
    );
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

    $this->assertEquals((string) $dir, (string) $this->projectPackage->dir('root'));
    $this->assertEquals((string) $dir, (string) $this->projectPackage->getRootDirectory());
  }

  public function testCloneIsNotSensibleToRootDirectoryChanges() {
    $clonedPackage = clone $this->projectPackage;

    $this->projectPackage->setRootDirectory($dir = Dir::factoryTS(__DIR__));

    $this->assertEquals((string) $dir, (string) $this->projectPackage->getRootDirectory());
    
    $this->assertNotEquals((string) $dir, (string) $clonedPackage->getRootDirectory(), 'cloned directory should not change!');
    $this->assertNotEquals((string) $dir, (string) $clonedPackage->dir('root'), 'cloned directory root retrieved with dir() should not change!');
  }

  public function testIsBuiltAndsetBuilt() {
    $this->assertFalse($this->projectPackage->isBuilt());

    $this->projectPackage->setBuilt(TRUE);
    $this->assertTrue($this->projectPackage->isBuilt());

    $this->projectPackage->setBuilt(FALSE);
    $this->assertFalse($this->projectPackage->isBuilt());
  }

  public function testReturnsDirectorySetInDirectoryLocationsFromConfig() {
    $this->projectPackage->setConfiguration(
      new Configuration(array('directory-locations'=>array('doctrine-entities'=>'lib/ACME/Entities/')))
    );
    $this->projectPackage->getConfiguration()->set(array('directory-locations', 'doctrine-proxies'), 'files/cache/php/doctrine-proxies');
    $this->projectPackage->configurationUpdate();

    $this->assertEquals(
      (string) $this->projectPackage->getRootDirectory()->sub('lib/ACME/Entities/'),
      (string) $this->projectPackage->dir('doctrine-entities')
    );

    $this->assertEquals(
      (string) $this->projectPackage->getRootDirectory()->sub('files/cache/php/doctrine-proxies/'),
      (string) $this->projectPackage->dir('doctrine-proxies')
    );

  }

  public function testThrowsInvalidArgumentExceptionForNonDefinedPaths() {
    $this->setExpectedException('InvalidArgumentException');
    $this->projectPackage->dir('something-is-not-defined-here');
  }

  public function testCloneIsNotSensibleToDIrectoryLocationsChanges() {
    $clonedPackage = clone $this->projectPackage;

    $this->projectPackage->updateDirectoryLocations(array('new-and-prev-not-defined'=>$dir = Dir::factoryTS(__DIR__)));

    $this->setExpectedException('InvalidArgumentException');
    $clonedPackage->dir('new-and-prev-not-defined');
  }
}
