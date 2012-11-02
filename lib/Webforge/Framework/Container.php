<?php

namespace Webforge\Framework;

use Webforge\Setup\ApplicationStorage;
use Webforge\Code\Generator\ClassWriter;
use Webforge\Code\Generator\ClassReader;
use Webforge\Code\Generator\ClassElevator;
use Webforge\Code\GlobalClassFileMapper;
use Webforge\Code\Generator\ClassFileMapper;
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
   * @var Webforge\Code\Generator\ClassReader
   */
  protected $classReader;

  /**
   * @var Webforge\Code\Generator\ClassElevator
   */
  protected $classElevator;
  
  /**
   * @var Webforge\Code\GlobalClassFileMapper
   */
  protected $classFileMapper;

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
   * @return Webforge\Code\Generator\ClassReader
   */
  public function getClassReader() {
    if (!isset($this->classReader)) {
      $this->classReader = new ClassReader();
    }
    return $this->classReader;
  }

  /**
   * @return Webforge\Code\Generator\ClassElevator
   */
  public function getClassElevator() {
    if (!isset($this->classElevator)) {
      $this->classElevator = new ClassElevator(
        $this->getClassFileMapper(),
        $this->getClassReader()
      );
    }
    return $this->classElevator;
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
   * @return Webforge\Code\ClassFileMapper
   */
  public function getClassFileMapper() {
    if (!isset($this->classFileMapper)) {
      $this->classFileMapper = new GlobalClassFileMapper();
      $this->classFileMapper->setPackageRegistry($this->getPackageRegistry());
    }
    
    return $this->classFileMapper;
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
   * @param Webforge\Code\ClassFileMapper $classFileMapper
   * @chainable
   */
  public function setClassFileMapper(ClassFileMapper $classFileMapper) {
    $this->classFileMapper = $classFileMapper;
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
   * @param Webforge\Code\Generator\ClassReader $classReader
   * @chainable
   */
  public function setClassReader(ClassReader $classReader) {
    $this->classReader = $classReader;
    return $this;
  }

  /**
   * @param Webforge\Code\Generator\ClassElevator $classElevator
   * @chainable
   */
  public function setClassElevator(ClassElevator $classElevator) {
    $this->classElevator = $classElevator;
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