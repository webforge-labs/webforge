<?php

namespace Webforge\Framework;

use Webforge\Setup\Package\SimplePackage;

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
    $prop('globalClassFileMapper', 'Webforge\Code\GlobalClassFileMapper');
    $prop('packageRegistry', 'Webforge\Setup\Package\Registry');
    $prop('composerPackageReader', 'Webforge\Setup\Package\ComposerPackageReader');
    
    return $props;
  }
  
  public function testApplicationStorageHasApplicationStorageName() {
    $this->assertEquals($this->container->getApplicationStorageName(), $this->container->getApplicationStorage()->getName());
  }
  
  public function testGlobalClassFileMapperHasRegistry() {
    $this->assertSame($this->container->getPackageRegistry(), $this->container->getGlobalClassFileMapper()->getPackageRegistry());
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
              'webforge/webforge',
              $this->getTestDirectory('packages/Webforge')
            ),

            new SimplePackage(
              'acme/intranet-application',
              $this->getTestDirectory('packages/ACME')
            )
           ));
    
    $registry = $this->container->getPackageRegistry();
    $packages = $registry->getPackages();
    $this->assertCount(2, $packages, 'registry has the wrong number of packages');
    
    $this->assertArrayEquals(
      array('webforge/webforge', 'acme/intranet-application'),
      $this->reduceCollection($packages, 'slug')
    );
  }
}
?>