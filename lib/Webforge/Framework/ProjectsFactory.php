<?php

namespace Webforge\Framework;

use Webforge\Framework\Package\Package;
use Webforge\Framework\Package\ProjectPackage;
use Webforge\Framework\Package\ProjectUrls;
use Webforge\Configuration\ConfigurationReader;

class ProjectsFactory implements ContainerAware {

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

    if (!is_bool($isDevel = $this->hostConfig->get('development'))) {

      // note: this is legacy and is wrong for so many times: production is the other way round
      $isDevel = $this->hostConfig->get('production');
    }
    
    if ($isDevel) {
      $flags |= ProjectPackage::DEVELOPMENT;
    }

    // how to determine other staging? in extra config from composer? as a file? (in root) in config?

    $bridge = $this->container->getCMSBridge();
    $name = $bridge->getProjectName($package);
    $lowerName = $package->getSlug();

    $projectPackage = new ProjectPackage($package, $name, $lowerName, $flags, $this->getHost(), new ProjectUrls($this->hostConfig));
    $this->readConfiguration($package, $projectPackage);

    return $projectPackage;
  }

  protected function getHost() {
    if (!isset($this->host)) {
      $this->host = $this->hostConfig->get(
        'host', 
        isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : php_uname('n')
      );
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

  /**
   * @param Webforge\Framework\Container container
   * @chainable
   */
  public function setContainer(Container $container) {
    $this->container = $container;
    return $this;
  }
}
