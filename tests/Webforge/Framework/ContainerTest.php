<?php

namespace Webforge\Framework;

use Webforge\Framework\Package\SimplePackage;
use Webforge\Common\System\Dir;
use Webforge\Framework\Package\Package;
use Webforge\Console\InteractionHelper;

class ContainerTest extends \Webforge\Code\Test\Base {
  
  protected $container;
  
  public function setUp() {
    $this->container = new Container();
  }
  
  /**
   * @dataProvider props
   */
  public function testInstanceOfProperty($propertyName, $fqn) {
    $getter = 'get'.ucfirst($propertyName);
    $this->assertInstanceOf($fqn, $this->container->$getter(), $propertyName.' has the wrong instance');
  }
  
  public static function props() {
    $props = array();
    $prop = function ($propertyName, $classFQN) use (&$props) {
      $props[] = array($propertyName, $classFQN);
    };
    
    $prop('applicationStorage', 'Webforge\Setup\ApplicationStorage');
    $prop('classWriter', 'Webforge\Code\Generator\ClassWriter');
    $prop('classReader', 'Webforge\Code\Generator\ClassReader');
    $prop('classElevator', 'Webforge\Code\Generator\ClassElevator');
    $prop('classFileMapper', 'Webforge\Code\GlobalClassFileMapper');
    $prop('packageRegistry', 'Webforge\Framework\Package\Registry');
    $prop('composerPackageReader', 'Webforge\Framework\Package\ComposerPackageReader');
    $prop('resourceDirectory', 'Webforge\Common\System\Dir');
    $prop('cmsBridge', 'Webforge\Framework\PscCMSBridge');
    $prop('hostConfiguration', 'Webforge\Setup\Configuration');
    $prop('projectsFactory', 'Webforge\Framework\ProjectsFactory');
    
    return $props;
  }

  protected function getInteractionHelper() {
    return new InteractionHelper(
      $this->getMock('Symfony\Component\Console\Helper\DialogHelper'),
      $this->getMock('Symfony\Component\Console\Output\OutputInterface')
    );
  }

  public function testContainerConstructsPartsInstaller() {
    $this->assertInstanceOf(
      'Webforge\Setup\Installer\PartsInstaller',
      $this->container->getPartsInstaller($this->getInteractionHelper())
    );
  }

  public function testPartsInstallerHasSomeParts() {
    $partsInstaller = $this->container->getPartsInstaller($this->getInteractionHelper());
    
    $this->assertGreaterThan(0, count($partsInstaller->getParts()));
  }

  public function testApplicationStorageHasApplicationStorageName() {
    $this->assertEquals($this->container->getApplicationStorageName(), $this->container->getApplicationStorage()->getName());
  }
  
  public function testClassFileMapperHasRegistry() {
    $this->assertSame($this->container->getPackageRegistry(), $this->container->getClassFileMapper()->getPackageRegistry());
  }
  
  public function testClassElevatorHasReaderAndMapper() {
    $this->assertSame($this->container->getClassReader(), $this->container->getClassElevator()->getClassReader());
    $this->assertSame($this->container->getClassFileMapper(), $this->container->getClassElevator()->getClassFileMapper());
  }
  
  public function testPackageRegistryGetsPackagesAddedFromConfigFileInApplicationStoragePackagesConfigJSON() {
    $this->container->setApplicationStorage(
      $storage = $this->getMock('Webforge\Setup\ApplicationStorage', array('getDirectory'), array('webforge-test'))
    );
    $appDir = $this->getTestDirectory('Setup/.webforge-test/');
    
    $storage->expects($this->atLeastOnce())->method('getDirectory')
            ->will($this->returnCallback(
                    function ($subDir = '/') use ($appDir) {
                      return $appDir->sub($subDir);
                    }
                  ));
    
    $this->container->setComposerPackageReader(
      $reader = $this->getMock('Webforge\Framework\Package\ComposerPackageReader', array('fromDirectory'))
    );
    $reader->expects($this->exactly(2))->method('fromDirectory')
           ->will($this->onConsecutiveCalls(
            new SimplePackage(
              'webforge',
              'webforge',
              $this->getTestDirectory('packages/Webforge')
            ),

            new SimplePackage(
              'intranet-application',
              'acme',
              $this->getTestDirectory('packages/ACME')
            )
           ));
    
    $registry = $this->container->getPackageRegistry();
    $packages = $registry->getPackages();
    $this->assertCount(2, $packages, 'registry has the wrong number of packages');
    
    $this->assertArrayEquals(
      array('webforge/webforge', 'acme/intranet-application'),
      $this->pluck($packages, 'identifier')
    );
  }
      
  public function testResourceDirectoryIsTheWebforgeResourceDirectory() {
    $this->assertEquals(
      (string) Dir::factory(__DIR__.DIRECTORY_SEPARATOR)->sub('../../../resources')->resolvePath(),
      (string) $this->container->getResourceDirectory()
    );
  }
  
  public function testLocalPackageInitFromDirectory() {
    $this->assertNull($this->container->getLocalPackage());
    
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS(__DIR__));
    
    $this->testInstanceOfProperty('localPackage', 'Webforge\Framework\Package\Package');
  }
  
  public function testLocalPackageInitFromDirectory_throwsExceptionWhenPackageIsNotFound() {
    $this->assertNull($this->container->getLocalPackage());
    
    $this->setExpectedException('Webforge\Framework\Exception');
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS($nonRegisteredDir = sys_get_temp_dir()));
  }
  
  public function testContainerReturnsALegacyProjectForPSCCMS() {
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS(__DIR__));
    
    $this->assertInstanceOf('Psc\CMS\Project', $project = $this->container->getLocalProject());
    $package = $this->container->getLocalPackage();
  }
  
  public function testThatAErroneousPackageFromPackagesJSONDoesRemoveThePackageOrDoesSomethingUsefulWithIt() {
    $this->markTestIncomplete('resolve dependency for local storage and move the init package registry to some other place than container?');
  }

  public function testContainerThrowsAnExceptionIfVendorPackageisNotInstalled() {
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS(__DIR__));

    $this->setExpectedException('Webforge\Framework\VendorPackageInitException');

    $this->container->getVendorPackage('schnurps/schlurps');
  }

  public function testContainerCanGetPackageInstalledAsVendor() {
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS(__DIR__));

    $commonPackage = $this->container->getVendorPackage('webforge/common');

    $this->assertEquals('webforge/common', $commonPackage->getIdentifier());

    $this->assertEquals(
      (string) $this->container->getLocalPackage()->getDirectory(Package::VENDOR)->sub('webforge/common/'),
      (string) $commonPackage->getRootDirectory()
    );
  }
}
