<?php

namespace Webforge\Setup\Package;

use Webforge\Setup\AutoLoadInfo;

class SimplePackageTest extends \Webforge\Code\Test\Base {
  
  protected $simplePackage, $root;
  
  public function setUp() {
    $this->simplePackage = new SimplePackage('some-slug', 'some-vendor', $this->root = $this->getTestDirectory()->sub('packages/ACME/'), new AutoLoadInfo(array()));
  }
  
  public function testPackageIsAPackageInterface() {
    $this->assertInstanceOf('Webforge\Setup\Package\Package', $this->simplePackage);
  }
  
  public function testPackageReturnsAnAutoloadInfo() {
    $this->assertInstanceOf('Webforge\Setup\AutoLoadInfo', $this->simplePackage->getAutoLoadInfo());
  }
  
  public function testVendorIsJustheVendorNameOftheSlug() {
    $this->assertEquals('some-vendor', $this->simplePackage->getVendor());
  }
  
  public function testSlugIsOnlyTheNameOfThePackage() {
    $this->assertEquals('some-slug', $this->simplePackage->getSlug());
  }
  
  public function testGetDirectoryVorVendorReturnsTheVendorDirectory() {
    $this->assertInstanceOf('Webforge\Common\System\Dir', $vendor = $this->simplePackage->getDirectory(Package::VENDOR));
    $this->assertEquals(
      (string) $this->root->sub('vendor/'),
      (string) $vendor
    );
  }
}
?>