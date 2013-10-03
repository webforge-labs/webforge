<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\File;
use Webforge\Code\Generator\GClass;

class AddCLICmd extends Command {
  
  protected $flags;
  protected $contents;
  protected $destination;
  
  public function __construct(GClass $command) {
    $this->command = $command;
  }
  
  public function doExecute() {
    $this->info('Add new '.$this->command->getFQN().'() to your CLI-Application commands.');
  }
  
  public function describe() {
    return sprintf('Adding '.$this->command.' to the CLI Application');
  }
  
  public function getCLICommand() {
    return $this->command;
  }
}
