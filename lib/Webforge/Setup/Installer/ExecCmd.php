<?php

namespace Webforge\Setup\Installer;

class ExecCmd extends Command {
  
  protected $cmd;
  
  public function __construct($cmd) {
    $this->cmd = $cmd;
  }
  
  public function doExecute() {
    // @codeCoverageIgnoreStart
    return exec($this->cmd);
    // @codeCoverageIgnoreEnd
  }
  
  public function describe() {
    return sprintf("Executing: '%s'", $this->cmd);
  }
}
