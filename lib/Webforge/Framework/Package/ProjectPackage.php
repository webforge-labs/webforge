<?php

namespace Webforge\Framework\Package;

use Webforge\Setup\ConfigurationReader;

class ProjectPackage {

  protected $package;

  protected $configuration;

  public function __construct(Package $package) {
    $this->package = $package;
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
    $reader->setScope(array('package'=>$this->package));

    return $reader->fromPHPFile($this->getConfigurationFile());
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
}
