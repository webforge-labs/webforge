<?php

namespace Webforge\Framework;

use Webforge\Setup\Package\Package;
use Psc\CMS\Project;
use Psc\PSC;
use Psc\System\File;
use Psc\CMS\Configuration as PscConfiguration;
use Webforge\Setup\Configuration;
use Psc\Exception AS BridgeException;

class PscCMSBridge {
  
  /**
   * @var Webforge\Setup\Configuration
   */
  protected $hostConfig;

  /**
   * @var Psc\System\Dir
   */
  protected $hostConfigFile;
  
  /**
   * @return Psc\CMS\Project
   */
  public function createProjectFromPackage(Package $package) {
    $paths = array();

    $paths[PSC::PATH_SRC] = './lib/';
    $paths[PSC::PATH_HTDOCS] = './www/';
    $paths[PSC::PATH_BASE] = './';
    $paths[PSC::PATH_CACHE] = './files/cache/';
    $paths[PSC::PATH_BIN] = './bin/';
    $paths[PSC::PATH_TPL] = './resources/tpl/';
    $paths[PSC::PATH_TESTDATA] = './tests/files/';
    $paths[PSC::PATH_TESTS] = './tests';
    $paths[PSC::PATH_CLASS] = './lib/'.$package->getSlug().'/';
    $paths[PSC::PATH_FILES] = './files/';
    $paths[PSC::PATH_BUILD] = './build/';
    
    $project = new Project(
      $package->getSlug(),
      $package->getRootDirectory(),
      $hostConfig = $this->getHostConfig(),
      $paths,
      $mode = Project::MODE_SRC,
      $staging = FALSE
    );
    
    return $project;
  }
  
  public function initLocalConfigurationFor(Project $project) {
    $project->initConfiguration($this->getLocalConfig($project));
    return $project;
  }
  
  public function getHostConfig(\Psc\CMS\ProjectsFactory $projectsFactory = NULL) {
    $projectsFactory = $projectsFactory ?: $this->getProjectsFactory();
    if (isset($projectsFactory)) {
      $this->hostConfig = $projectsFactory->getHostConfig();
    } else {
      $this->hostConfig = $this->readConfigurationFromFile($this->getHostConfigFile());
    }
    
    return $this->hostConfig;
  }
  
  protected function getLocalConfig(Project $project) {
    $localConfigFile = $this->getLocalConfigFile($project);
    
    if ($localConfigFile !== NULL) {
      return $this->readConfigurationFromFile($localConfigFile);
    } else {
      $conf = array();
      return new Configuration($conf);
    }
  }
  
  protected function readConfigurationFromFile(File $configFile) {
    require $configFile;
      
    if (!isset($conf)) {
      throw new BridgeException(
        sprintf("Config-File '%s' does not define \$conf. Even if its empty it should define \$conf as empty array.", $configFile)
      );
    }
    
    return new Configuration($conf);
  }
  
  protected function getProjectsFactory() {
    try {
      return PSC::getProjectsFactory();
    } catch (\Psc\Exception $e) {
      return NULL;
    }
  }
  
  protected function getHostConfigFile() {
    if (!isset($this->hostConfigFile)) {
      $this->hostConfigFile = PSC::getRoot()->getFile('host-config.php');
    }
    
    return $this->hostConfigFile;
  }
  
  public function setHostConfigFile(File $hcf) {
    $this->hostConfigFile = $hcf;
    return $this;
  }
  
  /**
   * @return File|NULL
   */
  protected function getLocalConfigFile(Project $project) {
    $packageConfigFile = $project->getRoot()->getFile('application/inc.config.php');
    
    if ($packageConfigFile->exists()) {
      return $packageConfigFile;
    }
    
    $projectConfigFile = $project->getSrc()->getFile('inc.config.php');
    
    if ($projectConfigFile->exists()) {
      return $projectConfigFile;
    }
    
    $baseConfigFile = $project->getBase()->getFile('inc.config.php');
    
    if ($baseConfigFile->exists()) {
      return $baseConfigFile;
    }
    
    return NULL;
  }
}
?>