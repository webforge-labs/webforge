<?php

namespace Webforge\Setup\Package;

use Psc\System\Dir;
use Webforge\Setup\AutoLoadInfo;

class SimplePackage implements Package {
  
  /**
   * @var Psc\System\Dir
   */
  protected $rootDirectory;
  
  /**
   * @var string
   */
  protected $slug;

  /**
   * @var Webforge\Setup\AutoLoadInfo|NULL
   */
  protected $autoLoadInfo;
  
  public function __construct($slug, Dir $root, AutoLoadInfo $info = NULL) {
    $this->slug = $slug;
    $this->rootDirectory = $root;
    $this->autoLoadInfo = $info;
  }
  
  /**
   * @param string $slug
   * @chainable
   */
  public function setSlug($slug) {
    $this->slug = $slug;
    return $this;
  }

  /**
   * @return string
   */
  public function getSlug() {
    return $this->slug;
  }

  /**
   * @param Psc\System\Dir $rootDirectory
   * @chainable
   */
  public function setRootDirectory(Dir $rootDirectory) {
    $this->rootDirectory = $rootDirectory;
    return $this;
  }

  /**
   * @return Psc\System\Dir
   */
  public function getRootDirectory() {
    return $this->rootDirectory;
  }
  
  /**
   * @return Webforge\Setup\AutoLoadInfo
   */
  public function getAutoLoadInfo() {
    return $this->autoLoadInfo;
  }
  
  /**
   * @param Psc\Setup\AutoLoadInfo $info
   */
  public function setAutoLoadInfo(AutoLoadInfo $info) {
    $this->autoLoadInfo = $info;
    return $this;
  }
  
  public function __toString() {
    return $this->getSlug();
  }
}
?>