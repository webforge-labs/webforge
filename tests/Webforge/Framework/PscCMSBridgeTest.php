<?php

namespace Webforge\Framework;

use Webforge\Setup\Package\Registry;
use Psc\PSC;
use Psc\Exception as PscException;

class PscCMSBridgeTest extends \Webforge\Code\Test\Base {
  
  protected $registry, $package, $appPackage, $withoutAutoLoadPackage, $oldStylePackage;
  
  protected $bridge;
  
  protected $rollbackFactory;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\PscCMSBridge';
    parent::setUp();
    
    $this->registry = new Registry();
    $this->package = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACMELibrary/'));
    $this->withoutAutoLoadPackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/WithoutAutoLoad/'));
    $this->appPackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACME/'));
    $this->oldStylePackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/PscOldStyleProject/Umsetzung/base/src/'));
    
    
    $this->bridge = new PscCMSBridge();
    
    try {
      PSC::getProjectsFactory(); // throws exception if not exists
      
      $this->fail('projectsfactory should not be defined in this tests');
    } catch (PscException $e) {
      
    }
  }
  
  public function testCreateProjectFromPackage() {
    $project = $this->bridge->createProjectFromPackage($this->package);

    $this->assertInstanceOf('Psc\CMS\Project', $project);
    
    $this->assertEquals('library', $project->getName());
    $this->assertTrue($project->loadedFromPackage);
  }
  
  public function testBridgeInitsAConfigEvenIfThereIsNoLocalConfigFile() {
    $project = $this->bridge->createProjectFromPackage($this->package);
    
    $this->bridge->initLocalConfigurationFor($project);
    
    $this->assertInstanceOf('Psc\CMS\Configuration', $project->getConfiguration());
  }
  
  public function testBridgeInitsAConfigEvenIfThereIsNoEnvironmentVariableForPscCMSSet() {
    $env = getenv('PSC_CMS');
    putenv('PSC_CMS=""');
    
    $project = $this->bridge->createProjectFromPackage($this->package);
    $this->bridge->initLocalConfigurationFor($project);
    $this->assertInstanceOf('Psc\CMS\Configuration', $project->getConfiguration());
    
    putenv('PSC_CMS="'.$env.'"');
  }
  

  public function testBridgeThrowExceptionIfHostConfigFileisEmpty() {
    $this->bridge->setHostConfigFile($this->getFile('empty.php'));
    
    $this->setExpectedException('Psc\Exception');
    $this->bridge->getHostConfig();
  }
  
  public function testProjectCanBeInitWithALocalConfigurationFile() {
    $this->bridge->setHostConfigFile($this->getFile('host-config.php'));
    
    $project = $this->bridge->createProjectFromPackage($this->appPackage);
    
    $this->bridge->initLocalConfigurationFor($project);
    
    $config = $project->getConfiguration();
    $this->assertInstanceOf('Psc\CMS\Configuration', $project->getConfiguration());
    
    // one item from project/app config
    $this->assertEquals('appKeyValue', $config->get('appKey'));
    
    // one item from host-config defaults
    $this->assertEquals('HostDefaultValue', $config->get('defaultKey'));
    
    // one item overriden default by project/app config
    $this->assertEquals('appKeyValue2', $config->get('overridenKey'));
  }
  
  public function testGetHostConfiguration_isLoadedFromPscProjectsFactoryWhenInjected() {
    $this->assertInstanceOf('Psc\CMS\Configuration', $this->bridge->getHostConfig($this->rollbackFactory));
  }

  public function testGetHostConfiguration_isLoadedFromPscRootDirectory() {
    try {
      $this->assertFileExists(PSC::getRoot()->getFile('host-config.php'), 'PSC::getRoot() must not throw exception for this test. host-config.php should exist');
      $this->assertInstanceOf('Psc\CMS\Configuration', $this->bridge->getHostConfig());
    } catch (\Psc\MissingEnvironmentVariableException $e) {
      $this->markTestSkipped('this is a stupid test with legacy code with static dependencies. And this test is skipped because host-config is not defined on this host');
    }
  }

  public function testProjectWithOldStyleShouldHaveSrcAsThePackageRoot() {
    $project = $this->bridge->createProjectFromPackage($this->oldStylePackage);
    
    $this->assertEquals(
      (string) $this->oldStylePackage->getRootDirectory(),
      (string) $project->getSrc()
    );
  }
  
  public function testProjectCanBeInitEvenForPackagesWithoutAutoLoadInfo_thenClassPathIsLibPerDefault() {
    $project = $this->bridge->createProjectFromPackage($this->withoutAutoLoadPackage);
    
    $this->assertEquals(
      (string) $this->withoutAutoLoadPackage->getRootDirectory()->sub('lib/WithoutAutoload'),
      (string) $project->getClassPath(),
      'the project created from package does not have autoload info so it should just return lib/ per default'
    );
  }
}
?>