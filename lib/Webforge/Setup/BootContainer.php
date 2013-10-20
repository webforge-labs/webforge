<?php

namespace Webforge\Setup;

use Webforge\Framework\Container as WebforgeContainer;
use Webforge\Framework\LocalPackageInitException;
use Webforge\Common\System\Dir;

class BootContainer {

  public $webforge;

  protected $rootDirectory;

  public function __construct($rootDirectory) {
    $this->webforge = new WebforgeContainer();
    $this->initRootDirectory($rootDirectory);
  }

  public function init() {

  }

  public function getWebforge() {
    return $this->webforge;
  }

  protected function initRootDirectory($rootDirectory) {
    if ($rootDirectory instanceof Dir) {
      $this->rootDirectory = $rootDirectory;
    } else {
      $this->rootDirectory = Dir::factoryTS($rootDirectory);
    }
  }

  /**
   * Returns the package of the current bootet rootDirectory
   * 
   * @return Webforge\Framework\Package\Package
   */
  public function getPackage() {
    if (!isset($this->package)) {
      $this->initLocalWebforgePackage();
      $this->package = $this->webforge->getLocalPackage();
    }
    
    return $this->package;
  }

  /**
   * Tries to init the package with webforge automatically
   *
   * this can fail in some cases:
   *   1. the easiest way is to webforge register-package the package which should be bootstrapped
   *   2. the next way would be to provide a composer.json with autoload infos in the rootDirectory of the container
   *   3. some older projects can have their composer.json in root\Umsetzung\base\src but this is discouraged to use and can be removed in the future
   *
   * after this is called the local package should be registered in webforge container (getLocalPackage() / getLocalProject())
   */
  protected function initLocalWebforgePackage() {
    try {
      $this->webforge->initLocalPackageFromDirectory($this->rootDirectory);
    } catch (LocalPackageInitException $e) {
      // this could happen for packages that are not (yet) registered by webforge
      // but thats not a problem at first hand: we assume that we can find a composer.json somewhere
      
      $this->webforge->getPackageRegistry()->addComposerPackageFromDirectory(
        $this->rootDirectory
      );
      
      // try again to init (its faster to use $composerRoot here, allthough this->rootDirectory would do it anyway)
      $this->webforge->initLocalPackageFromDirectory($this->rootDirectory); 
    }  
  }
  
  /**
   * @return Webforge\Configuration\Configuration
   */
  public function getHostConfiguration() {
    return $this->webforge->getHostConfiguration();
  }

  /**
   * @return Webforge\Configuration\Configuration
   */
  public function getHostConfig() {
    return $this->getHostConfiguration();
  }
}
