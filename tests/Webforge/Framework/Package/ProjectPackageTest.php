<?php

namespace Webforge\Framework\Package;

class ProjectPackageTest extends \Webforge\Framework\Package\PackagesTestCase {

  protected $projectPackage;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\Package\\ProjectPackage';
    parent::setUp();

    $this->projectPackage = new ProjectPackage($this->configPackage);
    $this->projectPackageApplicationConfig = new ProjectPackage($this->appPackage);
    $this->projectPackageWithoutConfig = new ProjectPackage($this->package);
    $this->comun = new ProjectPackage($this->camelCasePackage);
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
  }
}
