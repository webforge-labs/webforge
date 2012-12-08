<?php

namespace Webforge\Setup\Package;

use Webforge\Setup\AutoLoadInfo;

class SimplePackageTest extends \Webforge\Code\Test\Base {
  
  protected $simplePackage;
  
  public function setUp() {
    $this->simplePackage = new SimplePackage('some-slug', 'some-vendor', $this->getTestDirectory()->sub('packages/ACME/'), new AutoLoadInfo(array()));
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
}
?>