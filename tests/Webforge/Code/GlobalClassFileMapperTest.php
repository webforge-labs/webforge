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
    
    xdebug_break();
    $actualFile = $this->mapper->getFile('ACME\IntranetApplication\Main');
    
    $expectedFile = $this->getFixtureFile('ACME', array('lib', 'ACME', 'IntranetApplication'), 'Main.php');
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
                        PackageNotFoundException::fromSearch(array('fqn'=>'searched for unkown fqn (not set in test)'))
                      )
                    );
  }
  
  protected function createPackage($slug, $dirName) {
    $package = new SimplePackage($slug,
                                 $this->getPackageDir($dirName),
                                 new AutoLoadInfo(
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