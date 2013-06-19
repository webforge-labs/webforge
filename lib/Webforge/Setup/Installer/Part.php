<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\File;
use Webforge\Common\System\Dir;
use Webforge\Framework\Package\Package;
use Webforge\Framework\Container;

/**
 * "implement" PackageAware to have the $package set to the local package
 */
abstract class Part {
  
  /**
   * @var string
   */
  protected $name;
  
  /**
   * @var Webforge\Framework\Package\Package
   */
  protected $package;

  /**
   * @var Webforge\Framework\Container
   */
  protected $container;
  
  public function __construct($name) {
    $this->name = $name;
  }
  
  /**
   * Makes all Actions for the part
   */
  abstract public function installTo(Dir $target, Installer $installer);
  
  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }
  
  /**
   * @param Webforge\Framework\Package\Package $package
   * @chainable
   */
  public function setPackage(Package $package) {
    $this->package = $package;
    return $this;
  }

  /**
   * @return Webforge\Framework\Package\Package
   */
  public function getPackage() {
    return $this->package;
  }

  /**
   * @return \Webforge\Framework\Container
   */
  public function getContainer() {
    return $this->container;
  }
  
  /**
   * @param \Webforge\Framework\Container container
   * @chainable
   */
  public function setContainer(Container $container) {
    $this->container = $container;
    return $this;
  }
}
