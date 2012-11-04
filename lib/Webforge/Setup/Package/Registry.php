<?php

namespace Webforge\Setup\Package;

use Psc\System\Dir;
use Psc\A;
use Webforge\Code\Generator\GClass;
use Webforge\Common\String;

class Registry {
  
  /**
   * @var Webforge\Setup\Package\ComposerPackageReader
   */
  protected $composerPackageReader;
  
  protected $packages = array();
  protected $prefixes = array();
  
  public function __construct(ComposerPackageReader $reader = NULL) {
    $this->composerPackageReader = $reader;
  }
  
  public function findByFQN($fqn) {
    $gClass = new GClass($fqn);
    $fqn = $gClass->getFQN();
    
    foreach ($this->prefixes as $prefix => $packages) {
      if (mb_strpos($fqn, $prefix) === 0) {
        return $this->resolveToOne($packages, $fqn);
      }
    }

    throw PackageNotFoundException::fromSearch(array('fqn'=>$fqn), array_keys($this->prefixes));
  }
  
  /**
   * Returns a package by slug
   *
   * the first package with the slug beginning with $slug is returned
   * @return Package|NULL
   */
  public function findBySlug($slug) {
    foreach ($this->packages as $package) {
      if (String::startsWith($package->getSlug(), $slug)) {
        return $package;
      }
    }
  }
  
  protected function resolveToOne(Array $packages, $fqn) {
    if (count($packages) == 1) {
      return current($packages);
    }
    
    // no algorithm yet:
    throw new \Psc\Exception(sprintf("Multiple Packages found for '%s'. This cannot be solved, yet.\nFound:\n%s",
                                     $fqn, A::implode($packages, "\n", function ($package) { return $package->getSlug().' '.$package->getRootDirectory(); })));
  }
  
  /**
   * @param Webforge\Setup\Package\Package $package
   */
  public function addPackage(Package $package) {
    $this->indexPackage($package);
    $this->packages[$package->getSlug()] = $package;
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
   * @return Packages[]
   */
  public function getPackages() {
    return $this->packages;
  }
  
  public function getPrefixes() {
    return $this->prefixes;
  }
  
  public function setComposerPackageReader(ComposerPackageReader $reader) {
    $this->composerPackageReader = $reader;
    return $this;
  }
  // @codeCoverageIgnoreEnd
}
?>