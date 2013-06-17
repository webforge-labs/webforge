<?php

namespace Webforge\Framework\Package;

use Webforge\Setup\ConfigurationReader;
use Webforge\Framework\PscCMSBridge;
use Webforge\Setup\MissingConfigVariableException;

class ProjectPackage implements \Webforge\Framework\Project {

  const STAGING = 0x000001;
  const DEVELOPMENT   = 0x000010;

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
   * @var bitmap
   */
  protected $mode;

  /**
   * @var array
   */
  protected $languages;

  public function __construct(Package $package, $mode = 0x000000) {
    $this->package = $package;
    $this->bridge = new PscCMSBridge();
    $this->mode = $mode;
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
      try {
        $this->host = $this->bridge->getHostConfig()->req('host');
      } catch (MissingConfigVariableException $e) {
        $this->host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : php_uname('n');
      }
    }

    return $this->host;
  }

  /**
   * @return bool
   */
  public function isStaging() {
    return (bool) ($this->mode & self::STAGING);
  }

  /**
   * @return bool
   */
  public function isDevelopment() {
    return (bool) ($this->mode & self::DEVELOPMENT);
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

  public function setBridge(PscCMSBridge $bridge) {
    $this->bridge = $bridge;
    return $this;
  }
}
