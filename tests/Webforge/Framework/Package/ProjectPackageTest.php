<?php

namespace Webforge\Framework\Package;

use Webforge\Setup\Configuration;
use Webforge\Setup\MissingConfigVariableException;

class ProjectPackageTest extends \Webforge\Framework\Package\PackagesTestCase {

  protected $projectPackage, $oldStyleProjectPackage, $projectPackageWithoutConfig, $projectPackageApplicationConfig;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\Package\\ProjectPackage';
    parent::setUp();

    $this->projectPackage = new ProjectPackage($this->configPackage); // ACMESuperBlog
    $this->projectPackageApplicationConfig = new ProjectPackage($this->appPackage);
    $this->projectPackageWithoutConfig = new ProjectPackage($this->package);
    $this->develPackage = $this->oldStyleProjectPackage = new ProjectPackage($this->oldStylePackage, ProjectPackage::DEVELOPMENT);
    $this->stagingPackage = $this->comun = new ProjectPackage($this->camelCasePackage, ProjectPackage::STAGING);
  }

  public function testProjectPackageCanReadItsConfigurationFromEtcConfig() {
    $this->assertInstanceOf('Webforge\Setup\Configuration', $configuration = $this->projectPackage->getConfiguration());

    $this->assertEquals('ACME SuperBlog', $configuration->get('project.title'));
    $this->assertEquals('super-blog', $configuration->get('db.default.user'));
    $this->assertEquals('super-blog', $configuration->get('db.default.database'));
  }

  public function testProjectPackageCanReadItsConfigurationFromApplicationConfig() {
    $this->assertInstanceOf('Webforge\Setup\Configuration', $configuration = $this->projectPackageApplicationConfig->getConfiguration());

    $this->assertEquals('ACME IntranetApplication', $configuration->get('project.name'));
  }

  public function testProjectPackageReturnsEmptyConfigurationForNonFoundConfig() {
    $this->assertInstanceOf('Webforge\Setup\Configuration', $configuration = $this->projectPackageWithoutConfig->getConfiguration());
  }

  public function testLowerProjectName() {
    $this->assertEquals('super-blog', $this->projectPackage->getLowerName());
    $this->assertEquals('intranet-application', $this->projectPackageApplicationConfig->getLowerName());
    $this->assertEquals('comun', $this->comun->getLowerName());
  }

  public function testProjectName() {
    $this->assertEquals('SuperBlog', $this->projectPackage->getName());
    $this->assertEquals('IntranetApplication', $this->projectPackageApplicationConfig->getName());
    $this->assertEquals('CoMun', $this->comun->getName());
  }

  public function testIsStaging() {
    $this->assertFalse($this->projectPackage->isStaging());
    $this->assertTrue($this->stagingPackage->isStaging());
  }

  public function testHasLanguagesAndADefaultLanguage() {
    $this->assertEquals(array('de', 'en'), $this->projectPackage->getLanguages());
    $this->assertEquals('de', $this->projectPackage->getDefaultLanguage());
  }

  public function testIsDevelopment() {
    $this->assertFalse($this->projectPackage->isDevelopment());
    $this->assertTrue($this->develPackage->isDevelopment());
  }

  public function testGetsStatusAsString() {
    $this->assertEquals('live', $this->projectPackage->getStatus());
    $this->assertEquals('development', $this->develPackage->getStatus());
    $this->assertEquals('staging', $this->stagingPackage->getStatus());
  }

  public function testGetHostReturnsStringFromConfig() {
    $this->bridge = $this->getMock('Webforge\Framework\PscCMSBridge');
    $this->projectPackage->setBridge($this->bridge);
    $this->bridge
      ->expects($this->once())->method('getHostConfig')
      ->will($this->returnValue(new Configuration(array('host'=>'testhost'))));

    $this->assertEquals('testhost', $host = $this->projectPackage->getHost());
  }

  public function testGetHostFallsBackToPHPUname() {
    $this->bridge = $this->getMock('Webforge\Framework\PscCMSBridge');
    $this->projectPackage->setBridge($this->bridge);
    $this->bridge
      ->expects($this->once())->method('getHostConfig')
      ->will($this->throwException(MissingConfigVariableException::fromKeys(array('host'))));

    $this->assertNotEmpty($host = $this->projectPackage->getHost());
  }

  public function testOldStyleProjectPackageCanReadItsConfigurationSourceConfig() {
    $this->assertInstanceOf('Webforge\Setup\Configuration', $configuration = $this->oldStyleProjectPackage->getConfiguration());

    $this->assertEquals(TRUE, $configuration->get(array('PscOldStyleProject', 'loaded')));
  }

  public function testGetRootDirectoryReturnsPackageRoot() {
    $this->assertSame(
      $this->projectPackage->getRootDirectory(),
      $this->configPackage-> getRootDirectory()
    );
  }
}
