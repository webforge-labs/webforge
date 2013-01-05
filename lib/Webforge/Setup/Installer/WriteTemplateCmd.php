<?php

namespace Webforge\Setup\Installer;

use Psc\System\File;
use Psc\TPL\TPL;

class WriteTemplateCmd extends Command {

  /**
   * @var Psc\System\File
   */
  protected $template;

  /**
   * @var Psc\System\File
   */
  protected $destination;
  
  /**
   * @var array
   */
  protected $vars;
  
  /**
   * @var bitmap
   */
  protected $flags;

  public function __construct(File $template, File $destination, Array $vars = array(), $flags = 0x000000) {
    $this->template = $template;
    $this->destination = $destination;
    $this->vars = $vars;
    $this->flags = $flags;
  }
    
  public function describe() {
    return sprintf("Writing Contents from '%s' to '%s' (%d variables set)", $this->template, $this->destination, count($this->vars));
  }
  
  public function execute() {
    if (($this->flags & self::IF_NOT_EXISTS) && $this->destination->exists()) {
      $this->warn('will not overwrite (per request): '.$this->destination);
      return $this;
    }
    
    $contents = TPL::miniTemplate($this->template->getContents(), $this->vars);
    
    $this->destination->writeContents($contents);
    
    return $this;
  }
  
  public function getDestination() {
    return $this->destination;
  }
  
  public function getTemplate() {
    return $this->template;
  }
}
?>