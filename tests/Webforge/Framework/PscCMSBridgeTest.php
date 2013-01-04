<?php

namespace Webforge\Framework;

use Webforge\Setup\Package\Registry;

class PscCMSBridgeTest extends \Webforge\Code\Test\Base {
  
  protected $registry, $package;
  
  protected $bridge;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\PscCMSBridge';
    parent::setUp();
    
    $this->registry = new Registry();
    $this->package = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACMELibrary/'));
    
    $this->bridge = new PscCMSBridge();
  }
  
  public function testCreateProjectFromPackage() {
    $project = $this->bridge->createProjectFromPackage($this->package);

    $this->assertInstanceOf('Psc\CMS\Project', $project);
    
    $this->assertEquals('library', $project->getName());
  }
}
?>