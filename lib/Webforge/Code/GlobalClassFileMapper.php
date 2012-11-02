<?php

namespace Webforge\Code;

use Webforge\Code\Generator\ClassFileMapper;
use Webforge\Code\Generator\GClass;
use Webforge\Setup\Package\Registry AS PackageRegistry;
use Webforge\Code\ClassNotFoundException;
use Psc\System\File;
use Psc\System\Dir;
use ComposerAutoloaderInit;
use Webforge\Setup\Package\PackageNotFoundException;

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
  const WITH_INCLUDE_PATH = 0x000002;
  
  /**
   * A Registry for Packages installed on the host (e.g.)
   * 
   * @var Webforge\Setup\Package\Registry
   */
  protected $packageRegistry;

  /**
   * @return GClass
   */
  public function getClass(File $file) {
    throw new \Psc\Code\NotImplementedException('not yet');
  }
  
  /**
   * @return Psc\System\File
   */
  public function getFile($fqn) {
    $fqn = $this->normalizeClassFQN($fqn);
    
    if (($file = $this->findWithRegistry($fqn)) != NULL) {
      return $file;
    }
    
    throw ClassFileNotFoundException::fromFQN($fqn);
  }
  
  /**
   * @return Psc\System\File|NULL
   */
  public function findWithRegistry($fqn) {
    if (isset($this->packageRegistry)) {
      try {
        $package = $this->packageRegistry->findByFQN($fqn);
      } catch(PackageNotFoundException $e) {
        return NULL;
      }
      
      $autoLoad = $package->getAutoLoadInfo();
      if (isset($autoLoad) && ($file = $autoLoad->getFile($fqn, $package->getRootDirectory()))) {
        return $this->validateFile($file, self::WITH_RESOLVING);
      } else {
        $e = ClassNotFoundException::fromFQN($fqn);
        $e->appendMessage(sprintf('. AutoLoading from package: %s is not defined. Cannot resolve to file.', $package));
        throw $e;
      }
    }
    
    return NULL;
  }
  
  protected function validateFile(File $file, $flags = 0x0000) {
    if ($flags & self::WITH_INCLUDE_PATH && $file->isRelative()) {
      throw new \Psc\Code\NotImplementedException('YAGNI: composer was smart enough');
    }
    
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
   * @param Webforge\Setup\Package\Registry $packageRegistry
   * @chainable
   */
  public function setPackageRegistry(PackageRegistry $packageRegistry) {
    $this->packageRegistry = $packageRegistry;
    return $this;
  }

  /**
   * @return Webforge\Setup\Package\Registry
   */
  public function getPackageRegistry() {
    return $this->packageRegistry;
  }
}
?>