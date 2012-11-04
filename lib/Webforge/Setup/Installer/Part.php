<?php

namespace Webforge\Setup\Installer;

use Psc\System\File;
use Psc\System\Dir;

abstract class Part {
  
  /**
   * @var string
   */
  protected $name;
  
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
}
?>