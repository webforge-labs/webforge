<?php

namespace Webforge;

use Psc\PSC;
use Webforge\Common\System\Dir;
use Webforge\Common\System\File;

class BoostrapAcceptanceInnerTest extends \Webforge\Code\Test\Base {

  protected $root;

  public function setUp() {
    $this->root = new Dir(__DIR__.DIRECTORY_SEPARATOR);
    
    try {
      $this->project = PSC::getProject();
    } catch (\Exception $e) {} // let the first test, tell us the story, but setup if everything okay
    
    $this->container = $GLOBALS['env']['container'];
  }
  
  public function testPSCProjectIsSetToGlobalPSC() {
    $this->assertInstanceOf('Webforge\Framework\Project', \Psc\PSC::getProject(), 'PSC::getProject() should be an Project instance');
  }
  
  public function testProjectHasTheDirectoryRootOnSrc() {
    $this->assertEquals(
      (string) $this->root,
      (string) $this->project->dir('root'),
      'root should be set correctly in project'
    );
  }
  
  public function testPscCMSContainerReturnsTheSameAsThePSCProject() {
    $this->assertSame(
      PSC::getProject(),
      $GLOBALS['env']['container']->getProject(),
      'project in container and in PSC should reference the same object'
    );
  }
  
  public function testCMSContainerReturnsAProjectsFactory() {
    $this->assertInstanceOf('Psc\CMS\ProjectsFactory', $factory = $this->container->getProjectsFactory());
  }

  public function testCMSContainerProjectsFactoryIsTheSameAsInPSC() {
    $this->assertSame(
      PSC::getProjectsFactory(),
      $factory = $this->container->getProjectsFactory(),
      'the factory in static class PSC and factory in container should be the same'
    );
  }

  public function testCMSContainerProjectsFactoryHasTheSameHostConfigAsTheContainer() {
    $this->assertSame(
      PSC::getProjectsFactory()->getHostConfig(),
      $this->container->getProjectsFactory()->getHostConfig(),
      'the factory host-config and the host-config in factory from container should have the same host-config'
    );
  }
}
