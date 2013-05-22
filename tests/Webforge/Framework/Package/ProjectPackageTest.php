<?php

namespace Webforge\Framework\Package;

class ProjectPackageTest extends \Webforge\Framework\Package\PackagesTestCase {

  protected $projectPackage;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\Package\\ProjectPackage';
    parent::setUp();

    $this->projectPackage = new ProjectPackage($this->configPackage);
    $this->projectPackageApplicationConfig = new ProjectPackage($this->appPackage);
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
}
