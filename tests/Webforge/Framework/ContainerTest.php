<?php

namespace Webforge\Framework;

use Webforge\Framework\Package\SimplePackage;
use Webforge\Common\System\Dir;
use Webforge\Common\System\File;
use Webforge\Framework\Package\Package;
use Webforge\Console\InteractionHelper;
use mockery as m;
use Webforge\Framework\Package\Registry;

class ContainerTest extends \Webforge\Framework\Package\PackagesTestCase {

  const WITH_LOCAL_PACKAGE = 0x000001;
  
  protected $container;
  
  public function setup() {
    parent::setup();

    $this->container = new Container();
    $this->output = $this->getMockbuilder('Webforge\Common\CommandOutput')->getMockForAbstractClass();
  }
  
  /**
   * @dataProvider props
   */
  public function testInstanceOfProperty($propertyName, $fqn, $flags) {
    if ($flags & self::WITH_LOCAL_PACKAGE) {
      $this->container->initLocalPackageFromDirectory(Dir::factoryTS(__DIR__));
    }

    $getter = 'get'.ucfirst($propertyName);
    $this->assertInstanceOf($fqn, $this->container->$getter(), $propertyName.' has the wrong instance');
  }
  
  public static function props() {
    $props = array();
    $prop = function ($propertyName, $classFQN, $flags = 0) use (&$props) {
      $props[] = array($propertyName, $classFQN, $flags);
    };
    
    $prop('applicationStorage', 'Webforge\Setup\ApplicationStorage');
    $prop('classWriter', 'Webforge\Code\Generator\ClassWriter');
    $prop('classReader', 'Webforge\Code\Generator\ClassReader');
    $prop('classElevator', 'Webforge\Code\Generator\ClassElevator');
    $prop('classFileMapper', 'Webforge\Code\GlobalClassFileMapper');
    $prop('packageRegistry', 'Webforge\Framework\Package\Registry');
    $prop('composerPackageReader', 'Webforge\Framework\Package\ComposerPackageReader');
    $prop('resourceDirectory', 'Webforge\Common\System\Dir');
    $prop('hostConfiguration', 'Webforge\Configuration\Configuration');
    $prop('projectsFactory', 'Webforge\Framework\ProjectsFactory');
    $prop('inflector', 'Webforge\Framework\Inflector');
    $prop('systemContainer', 'Webforge\Common\System\Container');

    // this is not possible on travis because of shallow clones
    //$prop('releaseManager', 'Liip\RMT\Application', self::WITH_LOCAL_PACKAGE);
    
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
      $this->container->getPartsInstaller($this->getInteractionHelper(), $this->output)
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
    
    $this->testInstanceOfProperty('localPackage', 'Webforge\Framework\Package\Package', 0);
  }
  
  public function testLocalPackageInitFromDirectory_throwsExceptionWhenPackageIsNotFound() {
    $this->assertNull($this->container->getLocalPackage());
    
    $this->setExpectedException('Webforge\Framework\Exception');
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS($nonRegisteredDir = sys_get_temp_dir()));
  }
  
  public function testContainerReturnsAProjectForThePackageIFAvaible() {
    $this->container->initLocalPackageFromDirectory(Dir::factoryTS(__DIR__));
    
    $this->assertInstanceOf('Webforge\Framework\Package\ProjectPackage', $project = $this->container->getLocalProject());
    $this->assertInstanceOf('Webforge\Framework\Project', $project = $this->container->getLocalProject());
  }
  
  public function testThatAnErroneousPackageFromPackagesJSON_IsRemovedOrSomethingUsefulIsDoneWithIt() {
    $this->container->setApplicationStorage(
      $applicationStorage = m::mock('Webforge\Setup\ApplicationStorage')
    );

    $applicationStorage
      ->shouldReceive('getFile')
      ->with('packages.json')
      ->andReturn(new File('does-not-exist'));

    $this->assertInstanceOf('Webforge\Framework\Package\Registry', $this->container->getPackageRegistry());
  }

  public function testAFailingApplicationStorageDoesNotStopWebforgeFromWorking() {
    $this->container->setApplicationStorage(
      $applicationStorage = m::mock('Webforge\Setup\ApplicationStorage')
    );

    $applicationStorage
      ->shouldReceive('getFile')
      ->andThrow('Webforge\Setup\StorageException', 'Cannot find your home directory');

    $this->assertInstanceOf('Webforge\Framework\Package\Registry', $this->container->getPackageRegistry());
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

  public function testSystemContainerReturnsASystemWithoutHavingALocalPackageInit() {
    $this->assertInstanceOf('Webforge\Common\System\System', $this->container->getSystemContainer()->getSystem());
  }

  public function testGetLocalPackagehasADirtyStagingHack() {
    $package = $this->createVirtualPackage('serien-loader');

    $package->getRootDirectory()->getFile('deploy-info.json')->writeContents(<<<'JSON'
{
  "isStaging": true
}
JSON
    );
    $this->container->setLocalPackage($package);
    
    $project = $this->container->getLocalProject();

    $this->assertTrue($project->isStaging(), 'Project is set to staging');
  }

  public function testReturnsTheDeployInfoForAnPackageWithNoDeployInfoFile() {
    $info = $this->container->getDeployInfo($this->package);

    $this->assertObjectHasAttribute('isStaging', $info);
    $this->assertObjectHasAttribute('isDevelopment', $info);
    $this->assertObjectHasAttribute('isBuilt', $info);

    $this->assertNull($info->isStaging);
    $this->assertNull($info->isDevelopment);
    $this->assertNull($info->isBuilt);
  }

  public function testReturnsTheDeployInfoForAnPackageWithDeployInfoFile() {
    $info = $this->container->getDeployInfo($this->deployInfoPackage);

    $this->assertObjectHasAttribute('isStaging', $info);
    $this->assertObjectHasAttribute('isDevelopment', $info);
    $this->assertObjectHasAttribute('isBuilt', $info);

    $this->assertTrue($info->isStaging);
    $this->assertNull($info->isDevelopment);
    $this->assertTrue($info->isBuilt);
  }
}
