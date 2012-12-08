<?php

namespace Webforge\Setup\Installer;

use Psc\System\Dir;
use Psc\System\File;
use Webforge\Framework\ContainerAware;
use Webforge\Framework\Container;
use Webforge\Setup\Package\PackageAware;

/**
 * @todo an output interface to communicate and warn
 */
class PartsInstaller implements Installer {

  /**
   * @var Parts[]
   */
  protected $parts;
  
  /**
   * @var Webforge\Framework\Container;
   */
  protected $container;

  /**
   * @param $container Container make sure that this container has a localPackage defined
   */
  public function __construct(Array $parts = array(), Container $container) {
    $this->parts = $parts;
    $this->container = $container;
  }
  
  public function install(Part $part, Dir $destination) {
    if ($part instanceof ContainerAware) {
      $part->setContainer($this->container);
    }

    if ($part instanceof PackageAware) {
      $part->setPackage($this->container->getLocalPackage());
    }
    
    $part->installTo($destination, $this);
  }
  
  public function copy($source, $destination, $flags = 0x000000) {
    if ($source instanceof File || $source instanceof Dir) {
      if (($flags & self::IF_NOT_EXISTS) && $destination->exists()) {
        $this->warn('will not overwrite (per request): '.$destination);
        return $this;
      }
      
      $source->copy($destination);
    }
    
    return $this;
  }
  
  public function write($contents, File $destination, $flags = 0x000000) {
    if ($destination instanceof File) {
      if (($flags & self::IF_NOT_EXISTS) && $destination->exists()) {
        $this->warn('will not overwrite (per request): '.$destination);
        return $this;
      }
      
      $destination->writeContents($contents);
    }
    
    return $this;
  }
  
  /**
   * @return Psc\System\Dir
   */
  public function getWebforgeResources() {
    return $this->container->getResourceDirectory();
  }
  
  /**
   * @return Psc\System\Dir
   */
  public function getInstallTemplates() {
    return $this->getWebforgeResources()->sub('installTemplates/');
  }
  
  public function execute($cmd) {
    // @TODO finish test and use a new System->execute() command with symfony process
    return system($cmd);
  }
  
  public function warn($msg) {
  }
  
  public function getPart($name) {
    $names = array();
    foreach ($this->parts as $part) {
      if ($part->getName() === $name) {
        return $part;
      }
      $names[] = $part->getName();
    }
    
    throw new \RuntimeException(
      sprintf("Part with name '%s' does not exist. Avaible parts are: %s", $name, implode(', ', $names))
    );
  }
  
  /**
   * @chainable
   */
  public function addPart(Part $part) {
    $this->parts[] = $part;
    return $this;
  }
  
  /**
   * @var array
   */
  public function getParts() {
    return $this->parts;
  }
}
?>