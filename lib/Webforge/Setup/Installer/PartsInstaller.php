<?php

namespace Webforge\Setup\Installer;

use Psc\System\Dir;
use Psc\System\File;
use Webforge\Framework\ContainerAware;
use Webforge\Framework\Container;

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
  
  public function __construct(Array $parts = array(), Container $container) {
    $this->parts = $parts;
    $this->container = $container;
  }
  
  public function install(Part $part, Dir $destination) {
    if ($part instanceof ContainerAware) {
      $part->setContainer($this->container);
    }
    
    $part->installTo($destination, $this);
  }
  
  public function copy($source, $destination, $flags = 0x000000) {
    if ($source instanceof File || $source instanceof Dir) {
      if (($flags & self::IF_NOT_EXISTS) && $destination->exists()) {
        // warn?
        return $this;
      }
      
      $source->copy($destination);
    }
    
    return $this;
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