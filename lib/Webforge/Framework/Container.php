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
use Webforge\Setup\Installer\PartsInstaller;
use Symfony\Component\Console\Output\OutputInterface;

use Psc\JS\JSONConverter;
use Psc\System\Dir;

/**
 * This container includes the base classes for the framework
 *
 * its related to the webforge core-project
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
   * @var Webforge\Framework\PscCMSBridge
   */
  protected $cmsBridge;
  
  /**
   * The local package is the package for the current context
   * 
   * this is not necessary the package from webforge unless its called from webforge-core
   * @var Webforge\Setup\Package\Package
   */
  protected $localPackage;

  /**
   * The local project to the local package (legacy)
   * 
   * @var Psc\CMS\Project
   */
  protected $localProject;
  
  /**
   * @var Webforge\Setup\Package\ComposerPackageReader
   */
  protected $composerPackageReader;

  /**
   * @var Webforge\Setup\Installer\PartsInstaller
   */
  protected $partsInstaller;
  
  /**
   * @var Psc\System\Dir
   */
  protected $resourceDirectory;
  
  public function __construct() {
  }
  
  protected function initPackageRegistry(PackageRegistry $registry) {
    if (($packagesFile = $this->getApplicationStorage()->getFile('packages.json')) && $packagesFile->exists()) {
      $json = JSONConverter::create()->parseFile($packagesFile);
      
      foreach ($json as $package => $info) {
        if (is_string($info)) {
          $info = (object) array('path'=>$info);
        }
        
        try {
          $registry->addComposerPackageFromDirectory(Dir::factoryTS($info->path));
        } catch (\Psc\Exception $e) {
          $e->prependMessage(sprintf("Failed to load package '%s' from '%s'.", $package, $packagesFile));
          throw $e;
        }
      }
    }
    
    if ($registry->findByIdentifier('webforge/webforge') === NULL) {
      $registry->addComposerPackageFromDirectory(Dir::factoryTS(__DIR__)->sub('../../../')->resolvePath());
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

  /**
   * @return Webforge\Setup\Installer\PartsInstaller
   */
  public function getPartsInstaller(OutputInterface $output = NULL) {
    if (!isset($this->partsInstaller)) {
      $this->partsInstaller =
        new PartsInstaller(
          Array(
            new \Webforge\Setup\Installer\CreateCLIPart(),
            new \Webforge\Setup\Installer\CreateBootstrapPart(),
            new \Webforge\Setup\Installer\ApacheConfigurationPart(),
            new \Webforge\Setup\Installer\InstallTestSuitePart(),
            new \Webforge\Setup\Installer\WriteHtaccessPart(),
            new \Webforge\Setup\Installer\PscJSBoilerplatePart(),
            new \Webforge\Setup\Installer\InitConfigurationPart()
          ),
        $this,
        $output
      );
    }
    
    return $this->partsInstaller;
  }
  
  /**
   * @return Webforge\Setup\Package\Package
   */
  public function getLocalPackage() {
    return $this->localPackage;
  }
  
  /**
   * @return Psc\CMS\Project
   */
  public function getLocalProject() {
    if (!isset($this->localProject))  {
      $this->localProject = $this->getCMSBridge()->createProjectFromPackage($this->getLocalPackage());
      $this->getCMSBridge()->initLocalConfigurationFor($this->localProject);
    }
    
    return $this->localProject;
  }
  
  /**
   * @chainable
   */
  public function initLocalPackageFromDirectory(Dir $dir) {
    $this->localPackage = $this->getPackageRegistry()->findByDirectory($dir);
    
    if ($this->localPackage === NULL) {
      throw new Exception(
        sprintf("'Localpackage cannot be initialized from Dir: '%s'. Did you registered the directory with webforge?", $dir)
      );
    }
    
    return $this;
  }

  /**
   * @return Psc\System\Dir
   */
  public function getResourceDirectory() {
    if (!isset($this->resourceDirectory)) {
      $this->resourceDirectory = $this->getPackageRegistry()->findByIdentifier('webforge/webforge')->getRootDirectory()->sub('resources/');
    }
    return $this->resourceDirectory;
  }
  
  /**
   * @return Webforge\Framework\PscCMSBridge
   */
  public function getCMSBridge() {
    if (!isset($this->cmsBridge)) {
      $this->cmsBridge = new PscCMSBridge();    
    }
    return $this->cmsBridge;
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
  
  /**
   * @param Webforge\Setup\Installer\PartsInstaller $partsInstaller
   * @chainable
   */
  public function setPartsInstaller(PartsInstaller $partsInstaller) {
    $this->partsInstaller = $partsInstaller;
    return $this;
  }

  // @codeCoverageIgnoreEnd
}
?>