<?php

namespace Webforge\Setup\Package;

class ComposerPackageReaderTest extends \Webforge\Code\Test\Base {
  
  protected $reader;
  
  public function setUp() {
    $this->reader = new ComposerPackageReader();
  }

  public function testFromDirectoryReturnsAnPackageWithInfosFromTheComposerJSON() {
    $package = $this->reader->fromDirectory($dir = $this->getTestDirectory()->sub('packages/ACME/'));
    
    $this->assertInstanceOf('Webforge\Setup\Package\Package', $package);
    $this->assertEquals('acme/intranet-application', $package->getSlug());
    $this->assertEquals((string) $dir, (string) $package->getRootDirectory());
  }
  
  public function testFromDirectoryPackageAutoLoadInfoIsPreFilledFromACME() {
    $package = $this->reader->fromDirectory($dir = $this->getTestDirectory()->sub('packages/ACME/'));
    
    $this->assertInstanceOf('Webforge\Setup\Package\Package', $package);
    $this->assertInstanceOf('Webforge\Setup\AutoLoadInfo', $autoLoad = $package->getAutoLoadInfo());
    $this->assertEquals(
      array('ACME'=>array('lib/')),
      $autoLoad->getPrefixes(),
      'Prefixes from AutoLoadInfo for ACME is damaged'
    );
  }
}
?>