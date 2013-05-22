<?php

namespace Webforge\Framework\Package;

use Webforge\Setup\ConfigurationReader;
use Webforge\Framework\PscCMSBridge;

class ProjectPackage {

  protected $package;

  protected $configuration;

  /**
   * Lowercased name with dashes
   * 
   * @var string
   */
  protected $lowerName;

  /**
   * CamelCased Name
   * 
   * @var string
   */
  protected $name;

  public function __construct(Package $package) {
    $this->package = $package;
  }

  /**
   * Returns the project name in CamelCase
   * @return string
   */
  public function getName() {
    if (!isset($this->name)) {
      $bridge = new PscCMSBridge();
      $this->name = $bridge->getProjectName($this->package);
    }

    return $this->name;
  }


  /**
   * Returns a safe slug in lowercase
   * 
   * Camel Case Project Names will be separated with -
   * this is aequivalent to the package slug
   * @return string
   */
  public function getLowerName() {
    if (!isset($this->lowerName)) {
      $this->lowerName = $this->package->getSlug();
    }

    return $this->lowerName;
  }

  public function getConfiguration() {
    if (!isset($this->configuration)) {
      $this->configuration = $this->readConfiguration();
    }

    return $this->configuration;
  }

  /**
   * @return Webforge\Setup\Configuraton
   */
  protected function readConfiguration () {
    $reader = new ConfigurationReader();
    $reader->setScope(array('package'=>$this->package, 'project'=>$this));

    if ($configFile = $this->getConfigurationFile()) {
      return $reader->fromPHPFile($configFile);
    } else {
      return $reader->fromArray(array());
    }
  }

  /**
   * @return File|NULL
   */
  protected function getConfigurationFile() {
    $etcConfigFile = $this->package->getRootDirectory()->getFile('etc/config.php');
    
    if ($etcConfigFile->exists()) {
      return $etcConfigFile;
    }

    $packageConfigFile = $this->package->getRootDirectory()->getFile('application/inc.config.php');
    
    if ($packageConfigFile->exists()) {
      return $packageConfigFile;
    }

    return NULL;
  }

  /**
   * @return bool
   */
  public function isStaging() {
    return FALSE;
  }
}
