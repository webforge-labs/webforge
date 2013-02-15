<?php

namespace Webforge\Framework;

use Webforge\Framework\Package\Package;
use Psc\CMS\Project;
use Psc\PSC;
use Psc\CMS\ProjectsFactory;
use Webforge\Common\System\File;
use Webforge\Common\Preg;
use Psc\CMS\Configuration as PscConfiguration;
use Webforge\Setup\Configuration;
use Psc\Exception AS BridgeException;
use RuntimeException;

/**
 * @todo some inflector for namspaceify (where to put?)
 */
class PscCMSBridge {
  
  /**
   * @var Webforge\Setup\Configuration
   */
  protected $hostConfig;

  /**
   * @var Webforge\Common\System\Dir
   */
  protected $hostConfigFile;
  
  /**
   * @var Psc\CMS\ProjectsFactory
   */
  protected $projectsFactory;
  
  /**
   * @return Psc\CMS\Project
   */
  public function createProjectFromPackage(Package $package) {
    $projectsFactory = $this->getProjectsFactory();
    
    $paths = array();
    
    if ($this->isOldStylePackage($package)) {
      // notice: the package root and the project root for oldStyleProjects are vastly different!
      $projectRoot = $package->getRootDirectory()->sub('../../')->resolvePath(); // => base, umsetzung
      
      // we use the default paths from the old ProjectsFactory because they are fine if root is set correctly
      $paths = $projectsFactory->getProjectPaths($package->getSlug());
      
    } else {
      $paths[PSC::PATH_SRC] = './application/src/';
      $paths[PSC::PATH_HTDOCS] = './www/';
      $paths[PSC::PATH_BASE] = './';
      $paths[PSC::PATH_CACHE] = './files/cache/';
      $paths[PSC::PATH_BIN] = './bin/';
      $paths[PSC::PATH_TPL] = './application/tpl/';
      $paths[PSC::PATH_TESTDATA] = './tests/files/';
      $paths[PSC::PATH_TESTS] = './tests';
      $paths[PSC::PATH_CLASS] = '.'.$this->getPackageClassPath($package)->getUrl($package->getRootDirectory());
      $paths[PSC::PATH_FILES] = './files/';
      $paths[PSC::PATH_BUILD] = './build/';
      
      $projectRoot = $package->getRootDirectory();
    }
    
    foreach ($paths as $path => $value) {
      $projectsFactory->setProjectPath($package->getSlug(), $path, $value);
    }
    
    $project =
      $projectsFactory->getProjectInstance(
        $this->getProjectName($package),
        $projectRoot,
        $this->getHostConfig(),
        $paths,
        $mode = Project::MODE_SRC,
        $staging = FALSE
      )
    ;
    $project->loadedFromPackage = TRUE;
    
    return $project;
  }
  
  protected function getPackageClassPath(Package $package) {
    list ($namespace, $dir) = $this->getPackageNamespaceAndPath($package);
    
    return $dir->sub($namespace.'/');
  }
  
  protected function getProjectName(Package $package) {
    list($namespace, $dir) = $this->getPackageNamespaceAndPath($package);
    
    // use namespace if namespace is camel cased package slug
    if ($namespace !== $package->getSlug() && mb_strtolower($namespace) === mb_strtolower($package->getSlug())) {
      return $namespace;
    }
    
    return $package->getSlug();
  }
  
  /**
   * @return list(string $namespace, $dir rootLibraryPath)
   */
  protected function getPackageNamespaceAndPath(Package $package) {
    try {
      list ($namespace, $dir) = $package->getAutoLoadInfo()->getMainPrefixAndPath($package->getRootDirectory());
    } catch (RuntimeException $e) {
      // it might be possible that these package has no autoLoad defined for a main path
      // fallback to lib
      // create namespace from package slug
      $namespace = $this->namespaceify($package->getSlug());
      $dir = $package->getRootDirectory()->sub('lib/');
    }
    
    return array($namespace, $dir);
  }
  
  
  public function initLocalConfigurationFor(Project $project) {
    $project->initConfiguration($this->getLocalConfig($project));
    return $project;
  }
  
  public function getProjectsFactory() {
    if (!isset($this->projectsFactory)) {
      $this->projectsFactory = new ProjectsFactory($this->getHostConfig());
    }
    return $this->projectsFactory;
  }
  
  public function getHostConfig(\Psc\CMS\ProjectsFactory $projectsFactory = NULL) {
    if (!isset($this->hostConfig)) {
      $projectsFactory = $projectsFactory ?: $this->getPscProjectsFactory();
      if (isset($projectsFactory)) {
        $this->hostConfig = $projectsFactory->getHostConfig();
      } elseif ($hostConfigFile = $this->getHostConfigFile()) {
        $this->hostConfig = $this->readConfigurationFromFile($hostConfigFile);
      } else {
        $this->hostConfig = new Configuration(array());
      }
    }
    
    return $this->hostConfig;
  }
  
  protected function getLocalConfig(Project $project) {
    $localConfigFile = $this->getLocalConfigFile($project);
    
    if ($localConfigFile !== NULL) {
      return $this->readConfigurationFromFile($localConfigFile, $scope = array('project'=>$project));
    } else {
      $conf = array();
      return new Configuration($conf);
    }
  }
  
  protected function readConfigurationFromFile(File $configFile, Array $scope = array()) {
    extract($scope);
    
    require $configFile;
      
    if (!isset($conf)) {
      throw new BridgeException(
        sprintf("Config-File '%s' does not define \$conf. Even if its empty it should define \$conf as empty array.", $configFile)
      );
    }
    
    return new Configuration($conf);
  }
  
  protected function getPscProjectsFactory() {
    try {
      return PSC::getProjectsFactory();
    } catch (\Psc\Exception $e) {
      return NULL;
    }
  }
  
  protected function getHostConfigFile() {
    if (!isset($this->hostConfigFile)) {
      try {
        $this->hostConfigFile = PSC::getRoot()->getFile('host-config.php');
      } catch (\Psc\MissingEnvironmentVariableException $e) {
      }
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

  protected function namespaceify($string) {
    return ucfirst(Preg::replace_callback($string, '/\-([a-zA-Z])/', function ($match) {
      return mb_strtoupper($match[1]);
    }));
  }
  
  protected function isOldStylePackage(Package $package) {
    //dumb first:
    return $package->getRootDirectory()->up()->getName() === 'base';
  }
}
?>