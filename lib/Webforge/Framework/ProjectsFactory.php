<?php

namespace Webforge\Framework;

use Webforge\Framework\Package\Package;
use Webforge\Framework\Package\ProjectPackage;
use Webforge\Framework\Package\ProjectUrls;
use Webforge\Configuration\ConfigurationReader;
use Webforge\Common\StringUtil as S;

class ProjectsFactory {

  /**
   * @var Webforge\Framework\Container
   */
  protected $container;

  protected $host;

  protected $hostConfig;

  public function __construct(Container $container) {
    $this->container = $container;
    $this->hostConfig = $this->container->getHostConfiguration();
  }

  /**
   * @return Webforge\Framework\Project
   */
  public function fromPackage(Package $package) {
    $flags = 0;
    $deployInfo = $this->container->getDeployInfo($package);

    if ($this->decideStaging($package, $deployInfo)) {
      $flags |= ProjectPackage::STAGING;
    }

    if ($this->decideDevelopment($package, $deployInfo)) {
      $flags |= ProjectPackage::DEVELOPMENT;
    }

    if ($this->decideBuilt($package, $deployInfo)) {
      $flags |= ProjectPackage::BUILT;
    }

    $name = $this->getProjectName($package);
    $lowerName = $package->getSlug();

    $projectPackage = new ProjectPackage(
      $package, $name, $lowerName,
      $flags,
      $this->getHost(),
      new ProjectUrls($this->hostConfig),
      new DirectoryLocations($package->getRootDirectory(), array())
    );

    $this->readConfiguration($package, $projectPackage);

    return $projectPackage;
  }

  protected function decideStaging($package, $deployInfo) {
    return $deployInfo->isStaging !== NULL ? $deployInfo->isStaging : FALSE;
  }

  protected function decideBuilt($package, $deployInfo) {
    return $deployInfo->isBuilt !== NULL ? $deployInfo->isBuilt : FALSE;
  }

  protected function decideDevelopment($package, $deployInfo) {
    return $deployInfo->isDevelopment !== NULL ? $deployInfo->isDevelopment: $this->isHostDevelopment();
  }

  protected function isHostDevelopment() {
    if (!is_bool($isDevel = $this->hostConfig->get('development'))) {

      // note: this is legacy and is wrong for so many times: production is the other way round
      $isDevel = $this->hostConfig->get('production');
    }

    return $isDevel;
  }

  protected function getHost() {
    if (!isset($this->host)) {
      $this->host = $this->hostConfig->req('host');
    }

    return $this->host;
  }

  protected function readConfiguration(Package $package, ProjectPackage $projectPackage) {
    $reader = new ConfigurationReader();
    $reader->setScope(array('package'=>$package, 'project'=>$projectPackage));

    $config = $reader->fromArray(
      $this->hostConfig->get(array('defaults'), array())
    );

    if ($configFile = $this->getConfigurationFile($package)) {
      $config->merge($reader->fromPHPFile($configFile));
    }
    
    $projectPackage->setConfiguration($config);

    return $config;
  }

  /**
   * @return File|NULL
   */
  protected function getConfigurationFile(Package $package) {
    $etcConfigFile = $package->getRootDirectory()->getFile('etc/config.php');
    
    if ($etcConfigFile->exists()) {
      return $etcConfigFile;
    }

    $packageConfigFile = $package->getRootDirectory()->getFile('application/inc.config.php');
    
    if ($packageConfigFile->exists()) {
      return $packageConfigFile;
    }

    $projectConfigFile = $package->getRootDirectory()->getFile('inc.config.php');
    
    if ($projectConfigFile->exists()) {
      return $projectConfigFile;
    }

    return NULL;
  }

  protected function getProjectName(Package $package) {
    $namespace = $package->getNamespace();
    $slug = $package->getSlug();

    if ($namespace !== $slug) {
      
      // use namespace if namespace is camel cased package slug
      if (mb_strtolower($namespace) === mb_strtolower($slug)) {
        return $namespace;
      }

      $ccSlug = $this->container->getInflector()->namespaceify($slug);

      if (S::endsWith($namespace, $ccSlug)) {
        return $ccSlug;
      }
    }
    
    return $slug;
  }
}
