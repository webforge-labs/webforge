<?php

namespace Webforge\Framework;

use Webforge\Setup\Package\SimplePackage;
use Psc\System\Dir;

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
    $prop('packageRegistry', 'Webforge\Setup\Package\Registry');
    $prop('composerPackageReader', 'Webforge\Setup\Package\ComposerPackageReader');
    $prop('partsInstaller', 'Webforge\Setup\Installer\PartsInstaller');
    $prop('resourceDirectory', 'Psc\System\Dir');
    $prop('cmsBridge', 'Webforge\Framework\PscCMSBridge');
    
    return $props;
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
      $reader = $this->getMock('Webforge\Setup\Package\ComposerPackageReader', array('fromDirectory'))
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
  
    
  public function testPartsInstallerHasSomeParts() {
    $partsInstaller = $this->container->getPartsInstaller();
    
    $this->assertGreaterThan(0, count($partsInstaller->getParts()));
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
    
    $this->testInstanceOfProperty('localPackage', 'Webforge\Setup\Package\Package');
  }
  
  public function testContainerReturnsALegacyProjectForPSCCMS() {
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS(__DIR__));
    
    $this->assertInstanceOf('Psc\CMS\Project', $project = $this->container->getLocalProject());
    $package = $this->container->getLocalPackage();
  }
  
  public function testThatAErroneousPackageFromPackagesJSONDoesRemoveThePackageOrDoesSomethingUsefulWithIt() {
    $this->markTestSkipped('resolve dependency for local storage and move the init package registry to some other place than container?');
  }
}
?>