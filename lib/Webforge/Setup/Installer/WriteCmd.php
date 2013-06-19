<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\File;

class WriteCmd extends Command {
  
  protected $flags;
  protected $contents;
  protected $destination;
  
  public function __construct($contents, File $destination, $flags = 0x000000) {
    $this->destination = $destination;
    $this->contents = $contents;
    $this->flags = $flags;
  }
  
  public function execute() {
    if ($this->destination instanceof File) {
      if (($this->flags & self::IF_NOT_EXISTS) && $this->destination->exists()) {
        $this->warn('will not overwrite (per request): '.$this->destination);
        
        return $this;
      }
      
      $this->destination->writeContents($this->contents);
    }
  }
  
  public function describe() {
    return sprintf('writing contents to '.$this->destination);
  }
  
  public function getDestination() {
    return $this->destination;
  }
  
  public function getContents() {
    return $this->contents;
  }
}
