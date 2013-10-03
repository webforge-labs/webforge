<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\CommandOutput;

/**
 */
abstract class Command implements \Webforge\Common\Command {

  const IF_NOT_EXISTS = Installer::IF_NOT_EXISTS;

  protected $output;
  
  public function execute(CommandOutput $output) {
    $this->output = $output;
    return $this->doExecute($this->output);
  }

  protected function doExecute() {
  }
  
  protected function warn($msg) {
    $this->output->warn($msg);
  }

  protected function info($msg) {
    $this->output->msg($msg);
  }
}
