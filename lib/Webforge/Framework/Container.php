<?php

namespace Webforge\Framework;

use Webforge\Setup\ApplicationStorage;
use Webforge\Code\Generator\ClassWriter;
use Webforge\Code\Generator\ClassReader;
use Webforge\Code\Generator\ClassElevator;
use Webforge\Code\GlobalClassFileMapper;
use Webforge\Code\Generator\ClassFileMapper;
use Webforge\Framework\Package\Registry AS PackageRegistry;
use Webforge\Framework\Package\ComposerPackageReader;
use Webforge\Framework\Package\Package;
use Webforge\Framework\Inflector;
use Webforge\Setup\Installer\PartsInstaller;
use Webforge\Common\JS\JSONConverter;
use Webforge\Common\System\Dir;
use Webforge\Common\CommandOutput;
use Webforge\Common\Exception\MessageException;
use Webforge\Console\InteractionHelper;
use Webforge\Configuration\ConfigurationReader;
use Webforge\Common\System\Container as SystemContainer;
use Webforge\Common\System\ContainerConfiguration as SystemContainerConfiguration;
use Liip\RMT\Application as ReleaseManager;
use Webforge\Framework\Package\ProjectPackage;

/**
 * This container includes the base classes for the framework
 *
 * its related to the webforge core-project
 */
class Container implements SystemContainerConfiguration {
  
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
   * @var Webforge\Common\System\Container
   */
  protected $systemContainer;
    
  /**
   * @var Liip\RMT\Application
   */
  protected $releaseManager;  
  
  /**
   * A Registry for Packages installed on the host (e.g.)
   * 
   * @var Webforge\Framework\Package\Registry
   */
  protected $packageRegistry;

  /**
   * @var Webforge\Framework\ProjectsFactory
   */
  protected $projectsFactory;

  /**
   * @var Webforge\Framework\Inflector
   */
  protected $inflector;

  /**
   * @var Webforge\Configuration\Configuration
   */
  protected $hostConfiguration;
  
  /**
   * The local package is the package for the current context
   * 
   * this is not necessary the package from webforge unless its called from webforge-core
   * @var Webforge\Framework\Package\Package
   */
  protected $localPackage;

  /**
   * The local project to the local package (legacy)
   * 
   * @var Psc\CMS\Project
   */
  protected $localProject;
  
  /**
   * @var Webforge\Framework\Package\ComposerPackageReader
   */
  protected $composerPackageReader;

  /**
   * @var Webforge\Setup\Installer\PartsInstaller
   */
  protected $partsInstaller;
  
  /**
   * @var Webforge\Common\System\Dir
   */
  protected $resourceDirectory;
  
  public function __construct() {
  }
  
  protected function initPackageRegistry(PackageRegistry $registry) {
    try {
      $packagesFile = $this->getApplicationStorage()->getFile('packages.json');

      if ($packagesFile->exists()) {
        $json = JSONConverter::create()->parseFile($packagesFile);
      
        foreach ($json as $package => $info) {
          if (is_string($info)) {
            $info = (object) array('path'=>$info);
          }
        
          try {
            $registry->addComposerPackageFromDirectory(Dir::factoryTS($info->path));
          } catch (MessageException $e) {
            $e->prependMessage(sprintf("Failed to load package '%s' from '%s'.", $package, $packagesFile));
            throw $e;
          }
        }
      } //else: packages not loaded

    } catch (\Webforge\Setup\StorageException $e) {
      // home directory not set, etc: 
      // make a notice? store temporary? hint developer?
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
   * @return Webforge\Framework\Package\Registry
   */
  public function getPackageRegistry() {
    if (!isset($this->packageRegistry)) {
      $this->packageRegistry = new PackageRegistry($this->getComposerPackageReader());
      $this->initPackageRegistry($this->packageRegistry);
    }
    return $this->packageRegistry;
  }

  /**
   * @return Webforge\Framework\Package\ComposerPackageReader
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
  public function getPartsInstaller(InteractionHelper $interaction = NULL, CommandOutput $output = NULL) {
    if (!isset($this->partsInstaller)) {
      $this->partsInstaller =
        new PartsInstaller(
          array(
            new \Webforge\Setup\Installer\CreateCLIPart(),
            new \Webforge\Setup\Installer\CreateBootstrapPart(),
            new \Webforge\Setup\Installer\ApacheConfigurationPart(),
            new \Webforge\Setup\Installer\InstallTestSuitePart(),
            new \Webforge\Setup\Installer\TravisCIPart(),
            new \Webforge\Setup\Installer\WriteHtaccessPart(),
            new \Webforge\Setup\Installer\PscJSBoilerplatePart(),
            new \Webforge\Setup\Installer\InitConfigurationPart(),
            new \Webforge\Setup\Installer\InitDoctrinePart(),
            new \Webforge\Setup\Installer\CMSContainerPart(),
            new \Webforge\Setup\Installer\CMSCompilerPart()
          ),
          $this,
          $interaction,
          $output
        )
      ;
    }
    
    return $this->partsInstaller;
  }
  
  /**
   * @return Webforge\Framework\Package\Package
   */
  public function getLocalPackage() {
    return $this->localPackage;
  }
  
  /**
   * @return Webforge\Framework\Project
   */
  public function getLocalProject() {
    if (!isset($this->localProject))  {
      $this->localProject = $this->getProjectsFactory()->fromPackage($this->getLocalPackage());
    }
    
    return $this->localProject;
  }

  /**
   * Returns the deploy info written as a file in directory root
   * 
   * notice: the properties:
   *  .isStaging, .isDevelopment, .isBuilt
   * are ALWAYS set but they can be: NULL|FALSE|TRUE
   * meaning NULL that it was not set in json (or invalid)
   * @return object 
   */
  public function getDeployInfo($package) {
    $deployInfoFile = $package->getRootDirectory()->getFile('deploy-info.json');

    $deployInfo = new \stdClass;
    if ($deployInfoFile->exists()) {
      $deployInfo = JSONConverter::create()->parseFile($deployInfoFile);
    }

    if (!isset($deployInfo->isStaging)) {
      $deployInfo->isStaging = NULL;
    }

    if (!isset($deployInfo->isDevelopment)) {
      $deployInfo->isDevelopment = NULL;
    }

    if (!isset($deployInfo->isBuilt)) {
      $deployInfo->isBuilt = NULL;
    }

    return $deployInfo;
  }

  /**
   * Returns a "global" Configuration
   * 
   * for example executables are defined here
   */
  public function getConfiguration() {
    if ($this->getLocalPackage() === NULL) {
      $reader = new ConfigurationReader();

      // see ProjectsFactory
      return $reader->fromArray(
        $this->getHostConfiguration()->get(array('defaults'), array())
      );
    }
    
    return $this->getLocalProject()->getConfiguration();
  }
  
  /**
   * @chainable
   */
  public function initLocalPackageFromDirectory(Dir $dir) {
    $this->localPackage = $this->getPackageRegistry()->findByDirectory($dir);
    
    if ($this->localPackage === NULL) {
      throw new LocalPackageInitException(
        sprintf("'Localpackage cannot be initialized from Dir: '%s'. Did you registered the directory with webforge?", $dir)
      );
    }
    
    return $this;
  }

  /**
   * Gets an package which is installed as vendor in the (current *locale*) package
   * 
   * @param Package $searchIn if it is provided it uses this package. otherwise the local package
   * @return Package
   */
  public function getVendorPackage($packageIdentifier, Package $searchIn = NULL) {
    $package = $searchIn ?: $this->getLocalPackage();

    $vendor = $package->getDirectory(Package::VENDOR);
    $packageRoot = $vendor->sub($packageIdentifier.'/');

    $e = NULL;
    try {
      if ($packageRoot->exists()) {
        return $this->getComposerPackageReader()->fromDirectory($packageRoot);
      }

    } catch (\Exception $e) {
    }

    throw VendorPackageInitException::fromIdentifierAndVendor($packageIdentifier, $vendor, $e);
  }

  /**
   * @return Webforge\Common\System\Dir
   */
  public function getResourceDirectory() {
    if (!isset($this->resourceDirectory)) {
      $this->resourceDirectory = $this->getPackageRegistry()->findByIdentifier('webforge/webforge')->getRootDirectory()->sub('resources/');
    }
    return $this->resourceDirectory;
  }

  /**
   * Returns the package for the (somwhere) installed package from webforge
   * 
   * this works even if another local package is inited
   * @return Webforge\Framework\Package\Package
   */
  public function getWebforgePackage() {
    return $this->getPackageRegistry()->findByIdentifier('webforge/webforge');
  }
  
  /**
   * @return Webforge\Configuration\Configuration
   */
  public function getHostConfiguration() {
    if (!isset($this->hostConfiguration)) {
      // @TODO refactor this into a host-configuration-reader
      $reader = new ConfigurationReader();

      try {
        if (class_exists('Psc\PSC')) {
          $hostConfigFile = \Psc\PSC::getRoot()->getFile('host-config.php');

          $this->hostConfiguration = $reader->fromPHPFile($hostConfigFile);
        } else {
          $root = getenv('PSC_CMS');

          if (!empty($root)) {
            $this->hostConfiguration = $reader->fromPHPFile($root->getFile('host-config.php'));
          }
        }

      } catch (\Psc\MissingEnvironmentVariableException $e) {
      }

      if (!isset($this->hostConfiguration)) {
        $this->hostConfiguration = $reader->fromArray(array(
          'host'=>isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : php_uname('n'),
          'development'=>FALSE
        ));
      }
    }

    return $this->hostConfiguration;
  }
  
  /**
   * @return Webforge\Framework\Inflector
   */
  public function getInflector() {
    if (!isset($this->inflector)) {
      $this->inflector = new Inflector();
    }
    return $this->inflector;
  }

  /**
   * @return \Webforge\Framework\ProjectsFactory
   */
  public function getProjectsFactory() {
    if (!isset($this->projectsFactory)) {
      $this->projectsFactory = new ProjectsFactory($this);
    }
    return $this->projectsFactory;
  }

  /**
   * @return Webforge\Common\System\Container
   */
  public function getSystemContainer() {
    if (!isset($this->systemContainer)) {
      $this->systemContainer = new SystemContainer($this);
    }
    return $this->systemContainer;
  }

  /**
   * Returns the configuration for the ExecutableFinder in System
   * 
   * implemented from Common\System\ContainerConfiguration
   * @return array
   */
  public function forExecutableFinder() {
    return $this->getConfiguration()->get(array('executables'), array());
  }

  /**
   * @return Liip\RMT\Application
   */
  public function getReleaseManager() {
    if (!isset($this->releaseManager)) {
      if (!defined('RMT_ROOT_DIR')) {        
        define('RMT_ROOT_DIR', $this->getLocalPackage()->getRootDirectory()->getPath(Dir::WITHOUT_TRAILINGSLASH));
      }

      $this->releaseManager = new ReleaseManager();
    }
    return $this->releaseManager;
  }
  
  /**
   * @param Liip\RMT\Application $releaseManager
   * @chainable
   */
  public function setReleaseManager(ReleaseManager $releaseManager) {
    $this->releaseManager = $releaseManager;
    return $this;
  }
  
  // @codeCoverageIgnoreStart
  /**
   * @param Webforge\Common\System\Container $systemContainer
   * @chainable
   */
  public function setSystemContainer(Container $systemContainer) {
    $this->systemContainer = $systemContainer;
    return $this;
  }
  
  /**
   * @param Webforge\Framework\Inflector inflector
   * @chainable
   */
  public function setInflector(Inflector $inflector) {
    $this->inflector = $inflector;
    return $this;
  }

  /**
   * @param \Webforge\Framework\ProjectsFactory projectsFactory
   * @chainable
   */
  public function setProjectsFactory(ProjectsFactory $projectsFactory) {
    $this->projectsFactory = $projectsFactory;
    return $this;
  }

  /**
   * @param Webforge\Framework\Package\Registry $packageRegistry
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

  /**
   * @param Webforge\Framework\Package\Package localPackage
   * @chainable
   */
  public function setLocalPackage(Package $localPackage) {
    $this->localPackage = $localPackage;
    return $this;
  }

  // @codeCoverageIgnoreEnd

  public function setHostConfiguration($config) {
    $this->hostConfiguration = $config;
  }
}
