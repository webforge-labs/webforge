<?php

namespace Webforge\Code;

use Webforge\Code\Generator\ClassFileMapper;
use Webforge\Code\Generator\GClass;
use Webforge\Framework\Package\Registry AS PackageRegistry;
use Webforge\Framework\Package\Package;
use Webforge\Code\ClassNotFoundException;
use Webforge\Common\System\File;
use Webforge\Common\System\Dir;
use Webforge\Common\String as S;
use ComposerAutoloaderInit;
use Webforge\Framework\Package\PackageNotFoundException;

/**
 * The Global Class File Mapper finds the corrosponding file in a project on the local machine
 *
 * the main usage of this classFileMapper is to find autoLoading paths on a developer machine and map those paths to class files
 *
 * given: a full qualified classname
 * returns: a file where to store the class for the fqn
 *
 * it is usefull to have such a classmapper on a developer-machine because it enables
 * you to create classes in every project you work on, which does not have to be a webforge-package
 *
 * a nice approach to this complexity would be to use an array of agents, which try to find the file
 * and then evaluate the array of agents.
 *
 */
class GlobalClassFileMapper implements ClassFileMapper {
  
  const WITH_RESOLVING    = 0x000001;
  
  /**
   * A Registry for Packages installed on the host (e.g.)
   * 
   * @var Webforge\Framework\Package\Registry
   */
  protected $packageRegistry;

  /**
   * @return GClass
   */
  public function getClass(File $file) {
    throw new \Psc\Code\NotImplementedException('not yet');
  }
  
  /**
   * @return Webforge\Common\System\File
   */
  public function getFile($fqn) {
    $fqn = $this->normalizeClassFQN($fqn);
    
    if (($file = $this->findWithRegistry($fqn)) != NULL) {
      return $file;
    }
    
    throw ClassFileNotFoundException::fromFQN($fqn);
  }
  
  /**
   * @return Webforge\Common\System\File|NULL
   */
  public function findWithRegistry($fqn) {
    if (isset($this->packageRegistry)) {
      try {
        $package = $this->packageRegistry->findByFQN($fqn);
        
        return $this->findWithPackage($fqn, $package);
        
      } catch(PackageNotFoundException $e) {
        $e = ClassFileNotFoundException::fromPackageNotFoundException($fqn, $e);
        throw $e;
      }
    }
    
    return NULL;
  }
  
  public function findWithPackage($fqn, Package $package) {
    $autoLoad = $package->getAutoLoadInfo();

    if (!isset($autoLoad)) {
      $e = ClassNotFoundException::fromFQN($fqn);
      $e->appendMessage(sprintf('. AutoLoading from package: %s is not defined. Cannot resolve to file.', $package));
      throw $e;
    }
    
    $files = $autoLoad->getFiles($fqn, $package->getRootDirectory());
  
    if (count($files) === 0) {
      $e = ClassNotFoundException::fromFQN($fqn);
      $e->appendMessage(sprintf(". AutoLoading from package: %s failed. 0 files found.", $package));
      throw $e;
    }

    $file = $this->resolveConflictingFiles($files, $fqn, $package);

    return $this->validateFile($file, self::WITH_RESOLVING);
  }

  protected function resolveConflictingFiles(Array $files, $fqn, Package $package) {
    if (count($files) === 1) return current($files);

    $testsDir = $package->getDirectory(Package::TESTS);
    $testFiles = array_filter(
      $files,
      function ($file) use ($testsDir) {
        return $file->getDirectory()->isSubdirectoryOf($testsDir);
      }
    );

    if (S::endsWith($fqn, 'Test') && count($testFiles) === 1) {
      return current($testFiles);
    }
    
    $files = array_diff($files, $testFiles);
    
    if (count($files) === 1) return current($files);
    
    $e = ClassNotFoundException::fromFQN($fqn);
    $e->appendMessage(sprintf(". AutoLoading from package: %s failed. Too many Files were found:\n%s", $package, implode("\n", $files)));
    throw $e;
  }
  
  protected function validateFile(File $file, $flags = 0x0000) {
    if ($flags & self::WITH_RESOLVING) {
      $file->resolvePath();
    }
    
    return $file;
  }
  
  protected function normalizeClassFQN($fqn) {
    $fqn = ltrim($fqn, '\\');
  
    if (mb_strlen($fqn) === 0) {
      throw new \InvalidArgumentException('fqn cannot be empty');
    }
    
    return $fqn;
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
   * @return Webforge\Framework\Package\Registry
   */
  public function getPackageRegistry() {
    return $this->packageRegistry;
  }
}
?>