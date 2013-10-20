<?php

namespace Webforge\Framework;

use Webforge\Framework\Package\ProjectPackage;

/**
 * depends Webforge\Framework\ContainerTest, Webforge\Framework\PackageTest
 */
class ProjectsFactoryTest extends \Webforge\Framework\Package\PackagesTestCase {
  
  protected $projectPackage, $oldStyleProjectPackage, $projectPackageWithoutConfig, $projectPackageApplicationConfig;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\ProjectsFactory';
    parent::setUp();

    $this->container = new Container();
    $this->container->getHostConfiguration()
      ->set(array('production'), FALSE)
      ->set(array('development'), NULL)
      ->set(array('host'), 'testhost')
      ->set(array('defaults', 'db-secret'), 'host-db-secret')
      ->set(array('defaults', 'mail', 'from'), 'host@ps-webforge.com')
    ;
    $this->factory = $this->container->getProjectsFactory();

    $this->projectPackage = $this->factory->fromPackage($this->configPackage); // ACMESuperBlog
    $this->projectPackageApplicationConfig = $this->factory->fromPackage($this->appPackage);

    $this->stagingPackage = $this->comun = $this->factory->fromPackage($this->camelCasePackage);
    $this->stagingPackage->setStaging(TRUE);

    $this->container->getHostConfiguration()->set(array('development'), TRUE);
    $this->develPackage = $this->oldStyleProjectPackage = $this->factory->fromPackage($this->oldStylePackage);

    $this->container->getHostConfiguration()->set(array('host'), NULL);
    $this->projectPackageWithoutConfig = $this->factory->fromPackage($this->package);

    $this->projectBuiltPackage = $this->deployInfoPackage;
    $this->projectBuilt = $this->factory->fromPackage($this->deployInfoPackage);
  }

  public function testPreConditions() {
    $deployInfo = $this->container->getDeployInfo($this->projectBuiltPackage);
    $this->assertTrue($deployInfo->isStaging);
    $this->assertTrue($deployInfo->isBuilt);
    $this->assertNull($deployInfo->isDevelopment); // means just undefined
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

  public function testProjectGetsValuesInheritedFromHostConfigIntoProjectConfig() {
    $this->assertEquals('host-db-secret', $this->projectPackage->getConfiguration()->req(array('db-secret')));
  }

  public function testProjectCanOverridedInheritedValuesWithOwnConfig() {
    $this->assertEquals('info@acme.ps-webforge.com', $this->projectPackage->getConfiguration()->req(array('mail', 'from')));
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

  public function testProjectsFactoryReadsSettingsFromDeployInfoForpackage_AndOverwritesEverythingIfSet() {
    $this->assertTrue($this->projectBuilt->isStaging(), 'isStaging should be set because of deployInfo');
    $this->assertTrue($this->projectBuilt->isBuilt(), 'isBuilt should be set because of deployinfo');
  }

  public function testHostDevelopmentConfigurationIsUsedWhenDevelopmentInfoIsNull() {
    $this->assertTrue($this->projectBuilt->isDevelopment(), 'isDevelopment should be set because of host config');
  }

  public function testProjectWillHaveNamespaceInCamelCase_whenSlugEqualsLowerCasedCamelcaseNamespace() {
    $project = $this->factory->fromPackage($this->camelCasePackage);
    
    $this->assertEquals(
      'CoMun',
      $project->getNamespace(),
      'Namespace should be CamelCased'
    );

    $this->assertEquals(
      'CoMun',
      $project->getName(),
      'Name should be also CamelCased'
    );
  }

  public function testProjectWillHaveNamespaceInCamelCase_whenSluggedUnderscoresReplacedToCamelCaseEqualNamespace() {
    $project = $this->factory->FromPackage($this->underscorePackage);
    
    $this->assertEquals(
      'SerienLoader',
      $project->getName(),
      'Name should be CamelCased'
    );
    
    $this->assertEquals(
      'SerienLoader',
      $project->getNamespace(),
      'Namespace should be CamelCased'
    );
  }
}
