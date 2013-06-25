<?php

namespace Webforge\Framework;

/**
 * depends Webforge\Framework\ContainerTest, Webforge\Framework\PackageTest
 */
class ProjectsFactoryTest extends \Webforge\Framework\Package\PackagesTestCase {
  
  protected $projectPackage, $oldStyleProjectPackage, $projectPackageWithoutConfig, $projectPackageApplicationConfig;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\ProjectsFactory';
    parent::setUp();

    $this->container = new Container();
    $this->container->getHostConfiguration()->set(array('production'), FALSE);
    $this->container->getHostConfiguration()->set(array('development'), NULL);
    $this->container->getHostConfiguration()->set(array('host'), 'testhost');
    $this->factory = $this->container->getProjectsFactory();

    $this->projectPackage = $this->factory->fromPackage($this->configPackage); // ACMESuperBlog
    $this->projectPackageApplicationConfig = $this->factory->fromPackage($this->appPackage);

    $this->stagingPackage = $this->comun = $this->factory->fromPackage($this->camelCasePackage);
    $this->stagingPackage->setStaging(TRUE);

    $this->container->getHostConfiguration()->set(array('development'), TRUE);
    $this->develPackage = $this->oldStyleProjectPackage = $this->factory->fromPackage($this->oldStylePackage);

    $this->container->getHostConfiguration()->set(array('host'), NULL);
    $this->projectPackageWithoutConfig = $this->factory->fromPackage($this->package);
  }

  public function testProjectHasNamesAndConfigurationFromEtcConfig() {
    $this->assertInstanceOf('Webforge\Configuration\Configuration', $configuration = $this->projectPackage->getConfiguration());

    $this->assertEquals('ACME SuperBlog', $configuration->get('project.title'));
    $this->assertEquals('super-blog', $configuration->get('db.default.user'));
    $this->assertEquals('super-blog', $configuration->get('db.default.database'));
  }

  public function testProjectCanReadItsConfigurationFromApplicationConfig() {
    $this->assertInstanceOf('Webforge\Configuration\Configuration', $configuration = $this->projectPackageApplicationConfig->getConfiguration());

    $this->assertEquals('ACME IntranetApplication', $configuration->get('project.name'));
  }

  public function testProjectReturnsEmptyConfigurationForNonFoundConfig() {
    $this->assertInstanceOf('Webforge\Configuration\Configuration', $configuration = $this->projectPackageWithoutConfig->getConfiguration());
  }

  public function testLowerProjectNameIsEquivalentToPackageSlug() {
    $this->assertEquals('super-blog', $this->projectPackage->getLowerName());
    $this->assertEquals('intranet-application', $this->projectPackageApplicationConfig->getLowerName());
    $this->assertEquals('comun', $this->comun->getLowerName());
  }

  public function testProjectNameIsACamelCasedString() {
    $this->assertEquals('SuperBlog', $this->projectPackage->getName());
    $this->assertEquals('IntranetApplication', $this->projectPackageApplicationConfig->getName());
    $this->assertEquals('CoMun', $this->comun->getName());
  }

  public function testIsStagingIsNotYetDefinedHowToSet() {
    $this->assertFalse($this->projectPackage->isStaging());
    $this->assertTrue($this->stagingPackage->isStaging());
  }

  public function testHasLanguagesAndADefaultLanguageFromConfig() {
    $this->assertEquals(array('de', 'en'), $this->projectPackage->getLanguages());
    $this->assertEquals('de', $this->projectPackage->getDefaultLanguage());
  }

  public function testIsDevelopmentIsSetFromHostConfig() {
    $this->assertFalse($this->projectPackage->isDevelopment());
    $this->assertTrue($this->develPackage->isDevelopment());
  }

  public function testGetsStatusAsString() {
    $this->assertEquals('live', $this->projectPackage->getStatus());
    $this->assertEquals('development', $this->develPackage->getStatus());
    $this->assertEquals('staging', $this->stagingPackage->getStatus());
  }

  public function testGetHostReturnsStringFromConfig() {
    $this->assertEquals('testhost', $this->projectPackage->getHost());
  }

  public function testGetHostFallsBackToPHPUname() {
    $this->assertNotEmpty($host = $this->projectPackageWithoutConfig->getHost());
  }

  public function testOldStyleProjectPackageCanReadItsConfigurationSourceConfig() {
    $this->assertInstanceOf('Webforge\Configuration\Configuration', $configuration = $this->oldStyleProjectPackage->getConfiguration());

    $this->assertEquals(TRUE, $configuration->get(array('PscOldStyleProject', 'loaded')));
  }
}
