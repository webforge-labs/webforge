<?php

namespace Webforge\Framework\Package;

use Webforge\Setup\MissingConfigVariableException;
use Webforge\Configuration\Configuration;

class ProjectPackage implements \Webforge\Framework\Project {

  const STAGING = 0x000001;
  const DEVELOPMENT   = 0x000010;

  /**
   * @var Webforge\Framework\Package\Package
   */
  protected $package;

  /**
   * @var Webforge\Configuration\Configuration
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

  public function __construct(Package $package, $name, $lowerName, $mode = 0x000000, $host) {
    $this->name = $name;
    $this->lowerName = $lowerName;
    $this->package = $package;
    $this->mode = $mode;
    $this->host = $host;
  }

  /**
   * Returns the project name in CamelCase
   * @return string
   */
  public function getName() {
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
    return $this->lowerName;
  }

  /**
   * @return string
   */
  public function getNamespace() {
    return $this->package->getNamespace();
  }

  /**
   * @return Webforge\Setup\Configuraton
   */
  public function getConfiguration() {
    if (!isset($this->configuration)) {
      throw new \RuntimeException('Configuration is not yet defined! Factory error(!).');
    }

    return $this->configuration;
  }

  /**
   * @param Webforge\Setup\Configuraton $config
   */
  public function setConfiguration(Configuration $config) {
    $this->configuration = $config;
    return $this;
  }

  /**
   * @inherit-doc
   */
  public function getHost() {
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
    } elseif ($this->isDevelopment()) {
      $status = 'development';
    } else {
      $status = 'live';
    }

    return $status;
  }

  /**
   * @return array
   */
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

  /**
   * Returns a semantic directory for the project
   * 
   * avaible: test-files|cache|bin (for more see ProjectPackageTest)
   * @return Webforge\Common\System\Dir
   */
  public function dir($identifier) {
    return $this->package->getDirectory($identifier);
  }

  public function setStaging($to = TRUE) {
    if ($to) {
      $this->mode |= self::STAGING;
    } else {
      $this->mode &= ~self::STAGING;
    }
    return $this;
  }
}
