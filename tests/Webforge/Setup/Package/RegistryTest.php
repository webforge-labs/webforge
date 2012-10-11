<?php

namespace Webforge\Setup\Package;

class RegistryTest extends \Webforge\Code\Test\Base {
  
  protected $registry;
  
  public function setUp() {
    $this->registry = new Registry();
    
    $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACME/'));
  }
  
  public function testFindACMEByFQN() {
    $acmePackage = $this->registry->findByFQN('ACME\IntranetApplication\Main');
    
    $this->assertInstanceOf('Webforge\Setup\Package\Package', $acmePackage);
    $this->assertEquals('acme/intranet-application', $acmePackage->getSlug());
  }
}
?>