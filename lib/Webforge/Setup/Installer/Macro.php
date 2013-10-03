<?php

namespace Webforge\Setup\Installer;

use Webforge\Collections\ArrayCollection;


/**
 * @todo refactor to use common collection
 */
class Macro implements \Webforge\Common\Macro {
  
  protected $commands;
  
  public function __construct(Array $commands) {
    $this->commands = new ArrayCollection($commands);
  }
  
  public function execute() {
    foreach ($this->commands as $command) {
      $command->execute();
    }
    
    return $this;
  }
  
  public function getCommands() {
    return $this->commands->toArray();
  }
  
  public function addCommand(Command $cmd) {
    $this->commands->add($cmd);
    return $this;
  }
}
