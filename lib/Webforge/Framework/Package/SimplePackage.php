<?php

namespace Webforge\Framework\Package;

use Webforge\Common\System\Dir;
use Webforge\Setup\AutoLoadInfo;
use Webforge\Setup\NoAutoLoadPrefixException;
use Webforge\Framework\Inflector;

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
  
  /**
   * Caches the namespace
   * 
   * @var string
   */
  protected $namespace;

  /**
   * Caches the namespace directory
   * 
   * @var string
   */
  protected $namespaceDirectory;
  
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
  
  /**
   * @inherit-doc
   * @return string
   */
  public function getNamespace(Inflector $inflector = NULL) {
    if (!isset($this->namespace)) {
      try {
        list ($mainPrefix, $dir) = $this->autoLoadInfo->getMainPrefixAndPath($this->rootDirectory);
        
        $this->namespace = $mainPrefix;
        
      } catch (NoAutoLoadPrefixException $e) {
        $inflector = $inflector ?: new Inflector();
        
        // it might be possible that these package has no autoLoad defined for a main path
        $this->namespace = $inflector->namespaceify($this->slug);
      }
    }
    
    return $this->namespace;
  }

  /**
   * @inherit-doc
   * @return Webforge\Common\System\Dir
   */
  public function getNamespaceDirectory() {
    if (!isset($this->namespaceDirectory)) {
      try {
        list ($namespace, $dir) = $this->autoLoadInfo->getMainPrefixAndPath($this->rootDirectory);
        
      } catch (NoAutoLoadPrefixException $e) {
        // it might be possible that these package has no autoLoad defined for a main path
        // fallback to lib as namespace root
        $dir = $this->rootDirectory->sub('lib/');
      }
      
      $this->namespaceDirectory = $dir->sub($this->getNamespace().'/');
    }
    
    return $this->namespaceDirectory;
  }

  // @codeCoverageIgnoreStart
  /**
   * @param Psc\Setup\AutoLoadInfo $info
   */
  public function setAutoLoadInfo(AutoLoadInfo $info) {
    $this->namespace = $this->namespaceDirectory = NULL;
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
