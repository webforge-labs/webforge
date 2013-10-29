<?php

namespace Webforge\Framework\Package;

use Webforge\Common\System\Dir;
use Webforge\Common\ArrayUtil as A;
use Webforge\Code\Generator\GClass;
use Webforge\Common\String;
use Webforge\Common\ClassUtil;

class Registry {
  
  /**
   * @var Webforge\Framework\Package\ComposerPackageReader
   */
  protected $composerPackageReader;
  
  protected $packages = array();
  protected $prefixes = array();
  
  public function __construct(ComposerPackageReader $reader = NULL) {
    $this->composerPackageReader = $reader;
  }
  
  /**
   * @return Package
   * @throws PackageNotFoundException
   */
  public function findByFQN($fqn) {
    $gClass = new GClass($fqn);
    $fqn = $gClass->getFQN();
    
    foreach ($this->prefixes as $prefix => $packages) {
      if (mb_strpos($fqn, $prefix.'\\') === 0) {
        return $this->resolveToOne($packages, $fqn);
      }
    }

    throw PackageNotFoundException::fromSearch(array('fqn'=>$fqn), array_keys($this->prefixes));
  }
  
  /**
   * Returns a package by Identifier
   *
   * the first package with the identifier beginning with $identifier is returned
   * (but normally packages should be identifier-unique)
   * @return Package|NULL
   */
  public function findByIdentifier($identifier) {
    foreach ($this->packages as $package) {
      if (String::startsWith($package->getIdentifier(), $identifier)) {
        return $package;
      }
    }
  }
  
  /**
   * Returns a package by some of its sub directories
   *
   * @return Package|NULL
   */
  public function findByDirectory(Dir $directoryInPackage) {
    foreach ($this->packages as $package) {
      if ($directoryInPackage->isSubdirectoryOf($package->getRootDirectory())
          || $directoryInPackage->equals($package->getRootDirectory())
         ) {
        return $package;
      }
    }
  }
  
  protected function resolveToOne(Array $packages, $fqn) {
    if (count($packages) == 1) {
      return current($packages);
    }

    $log = '';

    try {
      return $this->resolveToOneWithPrimaryNamespace($packages, $fqn);
    
    } catch (NotResolvedException $e) {
      $log .= "\n".$e->getMessage();
    }    
    
    // no other ideas yet:
    throw new NotResolvedException(
      sprintf(
        "Multiple Packages found for '%s'. This cannot be solved, yet.\nFound:\n%s\nResults:%s",
        $fqn, 
        $this->dumpPackages($packages),
        $log
      )
    );
  }

  protected function dumpPackages($packages) {
    return A::implode(
      $packages, 
      "\n", 
      function ($package) { 
        return $package->getIdentifier().' '.$package->getRootDirectory(); 
      }
    );
  }

  protected function resolveToOneWithPrimaryNamespace(Array $packages, $fqn) {
    $ns = ClassUtil::getNamespace($fqn);
    $packagesWithPrimaryNamespace = array();
    foreach ($packages as $package) {
      if (mb_strpos($ns, $package->getNamespace()) === 0) { // package loads file with primary namespace
        $packagesWithPrimaryNamespace[] = $package;
      }
    }

    if (count($packagesWithPrimaryNamespace) == 1) {
      return current($packagesWithPrimaryNamespace);
    }

    throw new NotResolvedException(
      sprintf(
        "%d Packages match with the primary namespace to the fqn: %s. Packages:\n%s", 
        count($packagesWithPrimaryNamespace), 
        $fqn,
        $this->dumpPackages($packagesWithPrimaryNamespace)
      )
    );
  }
  
  /**
   * @param Webforge\Framework\Package\Package $package
   */
  public function addPackage(Package $package) {
    $this->indexPackage($package);
    $this->packages[$package->getIdentifier()] = $package;
    return $this;
  }
  
  protected function indexPackage(Package $package) {
    if ($package->getAutoLoadInfo() != NULL) {
      $newPrefix = FALSE;
      foreach ($package->getAutoLoadInfo()->getPrefixes() as $prefix => $paths) {
        if (!isset($this->prefixes[$prefix])) {
          $newPrefix = TRUE;
          $this->prefixes[$prefix] = array($package);
        } else {
          $this->prefixes[$prefix] = array_merge(
            $this->prefixes[$prefix],
            array($package)
          );
        }
      }
      
      if ($newPrefix) {
        krsort($this->prefixes);
      }
    }
  }
  
  /**
   * Adds a composer package from a directory
   */
   public function addComposerPackageFromDirectory(Dir $dir) {
    $package = $this->getComposerPackageReader()->fromDirectory($dir);
    
    $this->addPackage($package);
    
    return $package;
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
   * @return Packages[]
   */
  public function getPackages() {
    return $this->packages;
  }

  // @codeCoverageIgnoreStart
  public function getPrefixes() {
    return $this->prefixes;
  }
  
  public function setComposerPackageReader(ComposerPackageReader $reader) {
    $this->composerPackageReader = $reader;
    return $this;
  }
  // @codeCoverageIgnoreEnd
}
