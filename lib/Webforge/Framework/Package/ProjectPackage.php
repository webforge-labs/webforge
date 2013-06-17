<?php

namespace Webforge\Framework\Package;

use Webforge\Setup\ConfigurationReader;
use Webforge\Framework\PscCMSBridge;

class ProjectPackage implements \Webforge\Framework\Project {

  /**
   * @var Webforge\Framework\Package\Package
   */
  protected $package;

  /**
   * @var Webforge\Setup\Configuration
   */
  protected $configuration;

  /**
   * Lowercased name with dashes
   * 
   * @var string
   */
  protected $lowerName;

  /**
   * 
   * @var string
   */
  protected $name;

  /**
   * host where the project is run on
   */
  protected $host;

  /**
   * @var array
   */
  protected $languages;

  public function __construct(Package $package) {
    $this->package = $package;
    $this->bridge = new PscCMSBridge();
  }

  /**
   * Returns the project name in CamelCase
   * @return string
   */
  public function getName() {
    if (!isset($this->name)) {
      $this->name = $this->bridge->getProjectName($this->package);
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

  /**
   * @return Webforge\Setup\Configuraton
   */
  public function getConfiguration() {
    if (!isset($this->configuration)) {
      $this->configuration = $this->readConfiguration();
    }

    return $this->configuration;
  }

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

    $projectConfigFile = $this->package->getRootDirectory()->getFile('inc.config.php');
    
    if ($projectConfigFile->exists()) {
      return $projectConfigFile;
    }

    return NULL;
  }

  /**
   * @inherit-doc
   */
  public function getHost() {
    if (!isset($this->host)) {
      $this->host = $this->bridge->getHostConfig()->req(
        'host', 
        isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : php_uname('n')
      );
    }
    return $this->host;
  }

  /**
   * @return bool
   */
  public function isStaging() {
    return FALSE;
  }

  /**
   * @return bool
   */
  public function isDevelopment() {
    return FALSE;
  }

  /**
   * Returns the status of the project as a descriptive string
   */
  public function getStatus() {
    if ($this->isStaging()) {
      $status = 'staging';
    } elseif($this->isDevelopment()) {
      $status = 'development';
    } else {
      $status = 'live';
    }

    return $status;
  }

  public function getLanguages() {
    if (!isset($this->languages)) {
      $this->languages = $this->getConfiguration()->req(array('languages'));
    }

    return $this->languages;
  }

  public function getDefaultLanguage() {
    if (!isset($this->defaultLanguage)) {
      $this->defaultLanguage = current($this->getLanguages());
    }
    
    return $this->defaultLanguage;
  }

  /**
   * @return Webforge\Common\System\Dir
   */
  public function getRootDirectory() {
    return $this->package->getRootDirectory();
  }
}
