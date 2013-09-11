<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\File;
use Webforge\Code\Generator\CreateClassCommand;

/**
 * CreateClassCommand used in the PartsInstaller
 */
class CreateClassCmd extends WriteCmd {
  
  protected $flags;
  protected $createClassCommand;
  protected $destination;
  
  public function __construct(CreateClassCommand $cmd, File $destination = NULL, $flags = 0x000000) {
    $this->createClassCommand = $cmd;
    $this->flags = $flags;

    if ($destination) {
      $this->createClassCommand->setWriteFile($destination);
    }
  }
  
  public function execute() {
    if (($this->flags & self::IF_NOT_EXISTS) && $this->getDestination()->exists()) {
      $this->warn('will not overwrite (per request): '.$this->getDestination());
        
      return $this;
    }
      
    $this->createClassCommand->write($overwrite = TRUE);
  }
  
  public function describe() {
    return sprintf('creating a class to '.$this->destination);
  }
  
  public function getDestination() {
    return $this->destination instanceof File ? $this->destination : $this->createClassCommand->getFile();
  }
}
