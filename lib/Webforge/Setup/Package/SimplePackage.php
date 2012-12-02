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
   * @return string
   */
  public function getSlug() {
    return $this->slug;
  }
  
  /**
   * @return string
   */
  public function getTitle() {
    return ($pos = mb_strrpos($this->slug, '/')) !== FALSE
            ? ucfirst(mb_substr($this->slug, $pos+1))
            : $this->slug;
  }

  /**
   * @return Psc\System\Dir
   */
  public function getRootDirectory() {
    return $this->rootDirectory;
  }
  
  /**
   * @return Psc\System\Dir (cloned)
   */
  public function getDirectory($type = self::ROOT) {
    if ($type === self::ROOT) {
      return $this->getRootDirectory()->sub('/');
    } elseif ($type === self::TESTS) {
      return $this->getRootDirectory()->sub('/tests');
    }
  }
  
  /**
   * @return Webforge\Setup\AutoLoadInfo
   */
  public function getAutoLoadInfo() {
    return $this->autoLoadInfo;
  }
  
  // @codeCoverageIgnoreStart
  /**
   * @param Psc\Setup\AutoLoadInfo $info
   */
  public function setAutoLoadInfo(AutoLoadInfo $info) {
    $this->autoLoadInfo = $info;
    return $this;
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
   * @param string $slug
   * @chainable
   */
  public function setSlug($slug) {
    $this->slug = $slug;
    return $this;
  }

  public function __toString() {
    return $this->getSlug();
  }
  // @codeCoverageIgnoreEnd
}
?>