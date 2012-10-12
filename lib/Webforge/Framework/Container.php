<?php

namespace Webforge\Framework;

use Webforge\Setup\ApplicationStorage;
use Webforge\Code\Generator\ClassWriter;
use Webforge\Code\GlobalClassFileMapper;
use Webforge\Setup\Package\Registry AS PackageRegistry;
use Webforge\Setup\Package\ComposerPackageReader;

use Psc\JS\JSONConverter;
use Psc\System\Dir;

/**
 * This container includes the base classes for the framework
 *
 * 
 */
class Container {
  
  /**
   * @var string
   */
  protected $applicationStorageName = 'webforge';
  
  /**
   * @var Webforge\Setup\ApplicationStorage
   */
  protected $applicationStorage;

  /**
   * @var Webforge\Code\Generator\ClassWriter
   */
  protected $classWriter;
  
  /**
   * @var Webforge\Code\GlobalClassFileMapper
   */
  protected $globalClassFileMapper;

  /**
   * A Registry for Packages installed on the host (e.g.)
   * 
   * @var Webforge\Setup\Package\Registry
   */
  protected $packageRegistry;
  
  /**
   * @var Webforge\Setup\Package\ComposerPackageReader
   */
  protected $composerPackageReader;
  
  
  public function __construct() {
  }
  
  protected function initPackageRegistry(PackageRegistry $registry) {
    if (($packagesFile = $this->getApplicationStorage()->getFile('packages.json')) && $packagesFile->exists()) {
      $json = JSONConverter::create()->parseFile($packagesFile);
      
      foreach ($json as $package => $info) {
        if (is_string($info)) {
          $info = (object) array('path'=>$info);
        }
        
        $registry->addComposerPackageFromDirectory(Dir::factoryTS($info->path));
      }
    }
  }
  
  /**
   * @return Webforge\Code\Generator\ClassWriter
   */
  public function getClassWriter() {
    if (!isset($this->classWriter)) {
      $this->classWriter = new ClassWriter();
    }
    return $this->classWriter;
  }

  /**
   * @return Webforge\Setup\ApplicationStorage
   */
  public function getApplicationStorage() {
    if (!isset($this->applicationStorage)) {
      $this->applicationStorage = new ApplicationStorage($this->getApplicationStorageName());
    }
    
    return $this->applicationStorage;
  }
  
  /**
   * @return Webforge\Code\GlobalClassFileMapper
   */
  public function getGlobalClassFileMapper() {
    if (!isset($this->globalClassFileMapper)) {
      $this->globalClassFileMapper = new GlobalClassFileMapper();
      $this->globalClassFileMapper->setPackageRegistry($this->getPackageRegistry());
    }
    
    return $this->globalClassFileMapper;
  }

  /**
   * @return Webforge\Setup\Package\Registry
   */
  public function getPackageRegistry() {
    if (!isset($this->packageRegistry)) {
      $this->packageRegistry = new PackageRegistry($this->getComposerPackageReader());
      $this->initPackageRegistry($this->packageRegistry);
    }
    return $this->packageRegistry;
  }

    /**
   * @return Webforge\Setup\Package\ComposerPackageReader
   */
  public function getComposerPackageReader() {
    if (!isset($this->composerPackageReader)) {
      $this->composerPackageReader = new ComposerPackageReader();
    }
    return $this->composerPackageReader;
  }

  // @codeCoverageIgnoreStart
  /**
   * @param Webforge\Setup\Package\Registry $packageRegistry
   * @chainable
   */
  public function setPackageRegistry(PackageRegistry $packageRegistry) {
    $this->packageRegistry = $packageRegistry;
    return $this;
  }

  /**
   * @param Webforge\Code\GlobalClassFileMapper $globalClassFileMapper
   * @chainable
   */
  public function setGlobalClassFileMapper(GlobalClassFileMapper $globalClassFileMapper) {
    $this->globalClassFileMapper = $globalClassFileMapper;
    return $this;
  }

  /**
   * @param Webforge\Code\Generator\ClassWriter $classWriter
   * @chainable
   */
  public function setClassWriter(ClassWriter $classWriter) {
    $this->classWriter = $classWriter;
    return $this;
  }

  /**
   * @param Webforge\Setup\ApplicationStorage $applicationStorage
   * @chainable
   */
  public function setApplicationStorage(ApplicationStorage $applicationStorage) {
    $this->applicationStorage = $applicationStorage;
    return $this;
  }
  
  /**
   * @param string $applicationStorageName
   * @chainable
   */
  public function setApplicationStorageName($applicationStorageName) {
    $this->applicationStorageName = $applicationStorageName;
    return $this;
  }

  /**
   * @return string
   */
  public function getApplicationStorageName() {
    return $this->applicationStorageName;
  }

  public function setComposerPackageReader(ComposerPackageReader $reader) {
    $this->composerPackageReader = $reader;
    return $this;
  }
  // @codeCoverageIgnoreEnd
}
?>