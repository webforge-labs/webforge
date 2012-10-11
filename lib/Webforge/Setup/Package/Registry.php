<?php

namespace Webforge\Setup\Package;

use Psc\System\Dir;
use Webforge\Code\Generator\GClass;

class Registry {
  
  protected $packages = array();
  protected $prefixes = array();
  
  public function findByFQN($fqn) {
    $gClass = new GClass($fqn);
    $fqn = $gClass->getFQN();
    
    foreach ($this->prefixes as $prefix => $packages) {
      if (mb_strpos($fqn, $prefix) === 0) {
        return $this->resolveToOne($packages, $fqn);
      }
    }
    
    throw PackageNotFoundException::fromSearch(array('fqn'=>$fqn));
  }
  
  protected function resolveToOne(Array $packages, $fqn) {
    if (count($packages) == 1) {
      return current($packages);
    }
    
    // no algorithm yet:
    throw new \Psc\Exception(sprintf("Multiple Packages found for '%s'. This cannot be solved.", $fqn));
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
      foreach ($package->getAutoLoadInfo()->getPrefixes() as $prefix => $paths) {
        if (!isset($this->prefixes[$prefix])) {
          $this->prefixes[$prefix] = array($package);
        } else {
          $this->prefixes[$prefix] = array_merge(
            $this->prefixes[$prefix],
            array($package)
          );
        }
      }
    }
  }
  
  /**
   * Adds a composer package from a directory
   */
   public function addComposerPackageFromDirectory(Dir $dir) {
    /* i think it's a good idea to refactor it out to another class, but i can't find the name yet */
    $reader = new ComposerPackageReader();
    $package = $reader->fromDirectory($dir);
    
    $this->addPackage($package);
    
    return $package;
  }
}
?>