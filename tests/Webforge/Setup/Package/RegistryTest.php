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
    $this->assertEquals('acme/intranet-application', $acmePackage->getIdentifier());
  }
  
  /**
   * @expectedException Webforge\Setup\Package\PackageNotFoundException
   */
  public function testNonFindablePrefixFQNThrowsException() {
    $this->registry->findByFQN('BananenbaumisnotdefinedPrefix');
  }
  
  public function testFindACMEWithConflictingPackagesByFQN() {
    $registry = new Registry();
    
    // this is a project from ACME which is for the root autoloading namespace ACME\*
    // this is a libray for common things
    $registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACMELibrary/'));
    
    // the other Package (the naming is not very nice, i know..) is for the intranet-application and has
    // the autloading root namespace ACME\IntranetApplication
    $registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACME/'));
    
    // the library is added here first, because it would devour the namespace from ACME\IntranetApplication if not sorted
    $acmeIntranetPackage = $registry->findByFQN('ACME\IntranetApplication\Main');
    $this->assertEquals('acme/intranet-application', $acmeIntranetPackage->getIdentifier());

    $acmeLibPackage = $registry->findByFQN('ACME\Common\Util');
    $this->assertEquals('acme/library', $acmeLibPackage->getIdentifier());
  }
  
  public function testFindByIdentifier() {
    $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/Webforge/'));
    $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACMELibrary/'));
    
    $this->assertSame(
      $this->registry->findByFQN('ACME\Common\Util'),
      $this->registry->findByIdentifier('acme/library'),
      'acme library package is expected to be returned'
    );
  }
  
  public function testRegistryFindsProjectFromDirectoryWhichIsChildOfAprojectDirectory() {
    $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACMELibrary/'));
    
    $this->assertSame(
      $this->registry->findByDirectory($this->getTestDirectory()->sub('packages/ACMELibrary/tests/ACME')),
      $this->registry->findByIdentifier('acme/library'),
      'acme library package is expected to be returned, when find by path'
    );
  }
}
?>