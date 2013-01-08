<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Common\System\File;

class CopyCmd extends Command {
  
  /**
   * @var Webforge\Common\System\Dir|Webforge\Common\System\File
   */
  protected $source;

  /**
   * @var Webforge\Common\System\Dir|Webforge\Common\System\File
   */
  protected $destination;
  
  /**
   * @param Dir|File $source
   * @param Dir|File $destination if $destination is a Dir and $source is a file, the a file with $source->getName() will be copied to $destination
   */
  public function __construct($source, $destination, $flags = 0x000000) {
    $this->source = $source;
    $this->destination = $destination;
    $this->flags = $flags;
  }
  
  public function execute() {
    if ($this->source instanceof File && $this->destination instanceof File || $this->source instanceof Dir) {
      if (($this->flags & self::IF_NOT_EXISTS) && $this->destination->exists()) {
        $this->warn('will not overwrite (per request): '.$this->destination);
        
        return $this;
      }
    }
    
    if ($this->source instanceof Dir && $this->destination instanceof File) {
      throw new \InvalidArgumentException(
        sprintf("Cannot copy a dir to a file. '%s' to '%s'", $this->source, $this->destination)
      );
    }
    
    $this->source->copy($this->destination);
    
    return $this;
  }
  
  public function describe() {
    return sprintf("copy '%s' to '%s'", $this->source, $this->destination);
  }
  
  public function getSource() {
    return $this->source;
  }
  
  public function getDestination() {
    return $this->destination;
  }
}
?>