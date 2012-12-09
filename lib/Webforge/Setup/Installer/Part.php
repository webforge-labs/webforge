<?php

namespace Webforge\Setup\Installer;

use Psc\System\File;
use Psc\System\Dir;
use Webforge\Setup\Package\Package;

/**
 * "implement" PackageAware to have the $package set to the local package
 */
abstract class Part {
  
  /**
   * @var string
   */
  protected $name;
  
  /**
   * @var Webforge\Setup\Package\Package
   */
  protected $package;
  
  public function __construct($name) {
    $this->name = $name;
  }
  
  /**
   * Makes all Actions for the part
   */
  abstract public function installTo(Dir $target, Installer $installer);
  
  /**
   * @param string $name
   * @chainable
   */
  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }
  
  /**
   * @param Webforge\Setup\Package\Package $package
   * @chainable
   */
  public function setPackage(Package $package) {
    $this->package = $package;
    return $this;
  }

  /**
   * @return Webforge\Setup\Package\Package
   */
  public function getPackage() {
    return $this->package;
  }
}
?>