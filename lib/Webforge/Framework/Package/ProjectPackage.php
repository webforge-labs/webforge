<?php

namespace Webforge\Framework\Package;

use Webforge\Setup\MissingConfigVariableException;
use Webforge\Configuration\Configuration;
use Webforge\Common\System\Dir;
use Webforge\Framework\DirectoryLocations;

class ProjectPackage implements \Webforge\Framework\Project {

  const STAGING = 0x000001;
  const DEVELOPMENT   = 0x000010;
  const BUILT   = 0x000020;

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

  /**
   * The locations specific to the project (they do overwrite the ones from package)
   * 
   * @see dir()
   * @var Webforge\Framework\DirectoryLocations
   */
  protected $directoryLocations;

  /**
   * Upgrades a package to a new project
   */
  public function __construct(Package $package, $name, $lowerName, $mode = 0x000000, $host, ProjectURLs $urls, DirectoryLocations $directoryLocations) {
    $this->name = $name;
    $this->lowerName = $lowerName;
    $this->package = $package;
    $this->mode = $mode;
    $this->host = $host;
    $this->urls = $urls;
    $this->directoryLocations = $directoryLocations;
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
    $this->configurationUpdate();
    return $this;
  }

  /**
   * Should be called if configuration is changed
   * 
   * changed related paths are:
   *   .directory-locations
   */
  public function configurationUpdate() {
    $this->updateDirectoryLocations($this->configuration->get(array('directory-locations'), array()));
    return $this;
  }

  /**
   * Set new Locations for dir() from array
   */
  public function updateDirectoryLocations(Array $locations) {
    $this->directoryLocations->addMultiple($locations);
    return $this;
  }

  /**
   * @inherit-doc
   */
  public function defineDirectory($alias, $location) {
    $this->directoryLocations->set($alias, $location);
  }

  /**
   * @inherit-doc
   */
  public function getHost() {
    return $this->host;
  }

  /**
   * Returns an URL for the given type
   * 
   * urls associated with the project might be public url or a api url, something like that
   */
  public function getHostUrl($type = 'base') {
    return $this->urls->get($type, $this);
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
  public function isBuilt() {
    return (bool) ($this->mode & self::BUILT);
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
   * @return Webforge\Common\System\Dir
   */
  public function setRootDirectory(Dir $root) {
    return $this->package->setRootDirectory($root);
  }

  /**
   * Returns a semantic directory for the project
   * 
   * avaible: test-files|cache|bin (for more see ProjectPackageTest)
   * @return Webforge\Common\System\Dir
   */
  public function dir($identifier) {
    if ($identifier === 'root') {
      return $this->getRootDirectory();
    } elseif ($this->directoryLocations->has($identifier)) {
      return $this->directoryLocations->get($identifier);
    } else {
      return $this->package->getDirectory($identifier);
    }
  }

  public function setStaging($to = TRUE) {
    if ($to) {
      $this->mode |= self::STAGING;
    } else {
      $this->mode &= ~self::STAGING;
    }
    return $this;
  }

  public function setBuilt($to = TRUE) {
    if ($to) {
      $this->mode |= self::BUILT;
    } else {
      $this->mode &= ~self::BUILT;
    }
    return $this;
  }

  public function __clone() {
    $this->package = clone $this->package;
    $this->directoryLocations = clone $this->directoryLocations;
  }
}
