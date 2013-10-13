<?php

namespace Webforge\Framework\CLI;

use Webforge\Common\JS\JSONConverter;

class RegisterPackageTest extends CommandTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\CLI\\RegisterPackage';
    parent::setUp();

    $this->serienLoaderPackage = $this->underscorePackage;
    $this->serienLoaderRoot = $this->serienLoaderPackage->getRootDirectory();

    $this->registry = $this->mockContainerPackageRegistry(); // overwrite registry with mock
  }

  public function testWritesTheGivenDirectoryPackageWithSlugToThePackagesJsonFileInApplicationDir() {
    $this->expectSerienLoaderAddedToRegistry();
    $applicationStorage = $this->mockApplicationStorage();

    $this->execute((string) $this->serienLoaderRoot);

    $packages = JSONConverter::create()->parseFile($applicationStorage->getFile('packages.json'));

    $this->assertObjectHasAttribute('serien-loader', $packages, 'serien loader should be registered with slug');
    $this->assertEquals((string) $this->serienLoaderRoot, $packages->{'serien-loader'}, 'directory should be registered');
  }

  public function testWritesIntoExistingJsonFileAndDoesNotOverride() {
    $this->expectSerienLoaderAddedToRegistry();
    $applicationStorage = $this->mockApplicationStorage();

    $applicationStorage
      ->getFile('packages.json')
      ->writeContents('{
        "super-blog": "./some/path/defined"
      }');

    $this->execute((string) $this->serienLoaderRoot);

    $packages = JSONConverter::create()->parseFile($applicationStorage->getFile('packages.json'));

    $this->assertObjectHasAttribute('serien-loader', $packages, 'serien loader should be registered with slug');
    $this->assertEquals((string) $this->serienLoaderRoot, $packages->{'serien-loader'}, 'directory should be registered');

    $this->assertObjectHasAttribute('super-blog', $packages, 'previous registered package should not be overriden');
    $this->assertEquals('./some/path/defined', $packages->{'super-blog'}, 'directory should be registered');
  }

  public function testThrowsExceptionWhenTypeIsNotAllowed() {
    $this->setExpectedException('InvalidArgumentException');
    $this->execute('.', 'npm');
  }

  protected function expectSerienLoaderAddedToRegistry() {
    $root = $this->serienLoaderRoot;

    $this->registry
      ->expects($this->once())
      ->method('addComposerPackageFromDirectory')
      ->with($this->callback(function ($dir) use ($root) {
        return $dir->equals($root);
      }))
      ->will($this->returnValue($this->serienLoaderPackage));
  }

  protected function execute($location, $type = 'composer') {
    return $this->runCommand(
      'register-package', 
      array(
        'location'=>$location, 
        'type'=>$type
      )
    );
  }
}
