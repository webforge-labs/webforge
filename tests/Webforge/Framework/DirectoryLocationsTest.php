<?php

namespace Webforge\Framework;

use Webforge\Common\System\Dir;

class DirectoryLocationsTest extends \Webforge\Framework\Package\PackagesTestCase {

  protected $projectPackage;
  
  public function setUp() {
    parent::setUp();

    //$this->projectPackage = new Package\ProjectPackage($this->configPackage, 'ACMESuperBlog', 'super-blog', 0, 'psc');
    $this->package = $this->configPackage;
    $this->otherRoot = new Dir(__DIR__.DIRECTORY_SEPARATOR);

    $this->packageLocations = DirectoryLocations::createFromPackage($this->package);
  }

  public function testNotAvaibleLocationIdentifiersThrowAnException() {
    $this->setExpectedException('InvalidArgumentException');
    $this->packageLocations->get('humbug');
  }

  public function testRootIsAlwaysRegistered() {
    $dl = new DirectoryLocations($root = $this->otherRoot, array('something-unrelated'=>'sub/path/'));

    $this->assertEquals((string) $root, (string) $dl->get('root'));
    $this->assertNotSame($root, $dl->get('root'), 'should clone for root');
  }

  public function testSettingRootDirectoryToSomethingOtherChangesTheLocationsAsWell() {
    $this->packageLocations->setRoot($this->otherRoot);
    $this->assertEquals((string) $this->otherRoot, (string) $this->packageLocations->get('root'));
  }

  /**
   * @dataProvider provideSemanticPackageLocations
   */
  public function testItReturnsSemanticLocationsPerDir($identifier, $expectedPath) {
    $this->assertEquals(
      (string) $this->configPackage->getRootDirectory()->sub($expectedPath),
      (string) $this->packageLocations->get($identifier)
    );
  }

  public static function provideSemanticPackageLocations() {
    $tests = array();
  
    $test = function() use (&$tests) {
      $tests[] = func_get_args();
    };
  
    $test('test-files', 'tests/files/');
    $test('cache', 'files/cache/');
    $test('bin', 'bin/');
    $test('etc', 'etc/');
    $test('cms-uploads', 'files/uploads/');
    $test('cms-images', 'files/images/');
  
    return $tests;
  }
}
