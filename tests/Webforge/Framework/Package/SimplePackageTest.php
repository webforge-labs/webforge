<?php

namespace Webforge\Framework\Package;

use Webforge\Setup\AutoLoadInfo;

class SimplePackageTest extends \Webforge\Code\Test\Base {
  
  protected $simplePackage, $root;
  
  public function setUp() {
    $this->simplePackage = new SimplePackage(
      'some-slug',
      'some-vendor',
      $this->root = $this->getTestDirectory()->sub('packages/ACME/'),
      new AutoLoadInfo(array())
    );
  }
  
  public function testPackageIsAPackageInterface() {
    $this->assertInstanceOf('Webforge\Framework\Package\Package', $this->simplePackage);
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
  
  public function testGetNamespaceReturnsANamespacedNamedFromPackageSlug_byConvention_whenAutoloadInfoIsEmpty() {
    $this->simplePackage->setAutoLoadInfo(new AutoLoadInfo(array()));
    
    $this->assertEquals(
      'SomeSlug',
      $this->simplePackage->getNamespace()
    );

    $this->assertEquals(
      (string) $this->root->sub('lib/SomeSlug'),
      (string) $this->simplePackage->getNamespaceDirectory()
    );
  }
  
  public function testGetNamespaceReturnsNamespaceFromMainPrefixFromAutoLoadInfo_whenAutoloadInfoIsNotEmpty() {
    $this->simplePackage->setAutoLoadInfo(
      new AutoLoadInfo(
        json_decode('{"psr-0": {"SomeSlugAutoLoadNamespace": ["other-lib/"]}}')
      )
    );
    
    $this->assertEquals(
      'SomeSlugAutoLoadNamespace',
      $this->simplePackage->getNamespace()
    );

    $this->assertEquals(
      (string) $this->root->sub('other-lib/SomeSlugAutoLoadNamespace'),
      (string) $this->simplePackage->getNamespaceDirectory()
    );
  }
  
  public function testGetNamespaceIsChangedWhenNewAutoloadInfoIs_set() {
    $this->assertEquals(
      'SomeSlug',
      $this->simplePackage->getNamespace()
    );
    
    $this->simplePackage->setAutoLoadInfo(
      new AutoLoadInfo(
        json_decode('{"psr-0": {"SomeSlugAutoLoadNamespace": ["other-lib/"]}}')
      )
    );
    
    $this->assertEquals(
      'SomeSlugAutoLoadNamespace',
      $this->simplePackage->getNamespace(),
      'namespace should be changed, because new autoloadInfo was set'
    );
  }
}
?>