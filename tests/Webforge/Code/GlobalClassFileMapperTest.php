<?php

namespace Webforge\Code;

use Webforge\Code\Generator\ClassFileMapper;
use Webforge\Setup\Package\Package;
use Webforge\Setup\Package\PackageNotFoundException;
use Webforge\Setup\Package\SimplePackage;
use Webforge\Setup\AutoLoadInfo;

/**
 * tests the getFile() function from the GlobalClassFileMapper
 */
class GlobalClassFileMapperGetFileTest extends \Webforge\Code\Test\Base {
  
  protected $mapper;
  
  public function setUp() {
    $this->registry = $this->getMock('Webforge\Setup\Package\Registry', array('findByFQN'));
    $this->mapper = new GlobalClassFileMapper();
    $this->mapper->setPackageRegistry($this->registry);
  }
  
  public function testThatNonsenseFqnsCantGetFound() {
    $this->expectRegistryFindsNothing();
    $this->setExpectedException('Webforge\Code\ClassFileNotFoundException');
    $this->mapper->getFile('ths\class\has\a\nonsense\name\and\is\not\existent');
  }
  
  public function testEmptyFQNsAreBad() {
    $this->setExpectedException('InvalidArgumentException');
    $this->mapper->getFile('');
  }
  
  public function testSearchingWithRegistryForACMEnormalClass() {
    $this->expectRegistryFindsPackageForFQN(
      $this->createPackage('acme/intranet-application', 'ACME'),
      'ACME\IntranetApplication\Main'
    );
    
    $actualFile = $this->mapper->getFile('ACME\IntranetApplication\Main');
    $expectedFile = $this->getFixtureFile('ACME', array('lib', 'ACME', 'IntranetApplication'), 'Main.php');
    
    $this->assertEquals((string) $expectedFile, (string) $actualFile);
  }
  
  public function testAmbiguousAutoloadInfoGetsResolvedToNormalClass() {
    $this->expectRegistryFindsPackageForFQN(
      $this->createPackage(
        'webforge/webforge',
        'Webforge',
        Array(
          'psr-0'=>(object) array(
            'Webforge'=> array('lib/', 'tests/')
          )
        )
      ),
      'Webforge\Common\String'
    );
    
    $actualFile = $this->mapper->getFile('Webforge\Common\String');
    $expectedFile = $this->getFixtureFile('Webforge', array('lib', 'Webforge', 'Common'), 'String.php');
    
    $this->assertEquals((string) $expectedFile, (string) $actualFile);
  }
  
  protected function expectRegistryFindsPackageForFQN(Package $package, $fqn, $times = NULL) {
    $this->registry->expects($times ?: $this->once())->method('findByFQN')
                   ->with($this->equalTo($fqn))->will($this->returnValue($package));
  }
  
  protected function expectRegistryFindsNothing($times = NULL) {
    $this->registry->expects($times ?: $this->once())->method('findByFQN')
                   ->will(
                      $this->throwException(
                        PackageNotFoundException::fromSearch(array('fqn'=>'searched for unkown fqn (not set in test)'), array('somePrefix','someOtherPrefix'))
                      )
                    );
  }
  
  protected function createPackage($slug, $dirName, Array $autoLoadInfoSpec = NULL) {
    list($vendor, $slug) = explode('/', $slug, 2);
    $package = new SimplePackage($slug,
                                 $vendor,
                                 $this->getPackageDir($dirName),
                                 new AutoLoadInfo(
                                  $autoLoadInfoSpec ?: 
                                    Array(
                                     'psr-0'=>(object) array(
                                       'ACME'=> array('lib/')
                                      )
                                    )
                                  )
                                );
    return $package;
  }
  
  protected function getFixtureFile($package, $path, $fileName) {
    return $this->getTestDirectory('packages/'.$package.'/'.implode('/',$path).'/')->getFile($fileName);
  }
  
  protected function getPackageDir($dirName) {
    return $this->getTestDirectory('packages/'.$dirName.'/');
  }
}
?>