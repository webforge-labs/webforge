<?php

namespace Webforge\Framework\Package;

use Webforge\Common\System\Dir;
use Webforge\Setup\AutoLoadInfo;

class SimplePackage implements Package {
  
  /**
   * @var Webforge\Common\System\Dir
   */
  protected $rootDirectory;
  
  /**
   * @var string
   */
  protected $slug;
  
  /**
   * @var string
   */
  protected $vendor;

  /**
   * @var Webforge\Setup\AutoLoadInfo|NULL
   */
  protected $autoLoadInfo;
  
  public function __construct($slug, $vendor, Dir $root, AutoLoadInfo $info = NULL) {
    $this->slug = $slug;
    $this->vendor = $vendor;
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
  public function getVendor() {
    return $this->vendor;
  }

  /**
   * @return string vendor/slug
   */
  public function getIdentifier() {
    return $this->vendor.'/'.$this->slug;
  }
  
  /**
   * @return string
   */
  public function getTitle() {
    return ucfirst($this->slug);
  }
  

  /**
   * @return Webforge\Common\System\Dir
   */
  public function getRootDirectory() {
    return $this->rootDirectory;
  }
  
  /**
   * @return Webforge\Common\System\Dir (cloned)
   */
  public function getDirectory($type = self::ROOT) {
    if ($type === self::ROOT) {
      return $this->getRootDirectory()->sub('/');
    } elseif ($type === self::TESTS) {
      return $this->getRootDirectory()->sub('tests/');
    } elseif ($type === self::VENDOR) {
      return $this->getRootDirectory()->sub('vendor/');
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
   * @param Webforge\Common\System\Dir $rootDirectory
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