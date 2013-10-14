<?php

namespace Webforge\Framework\CLI;

use Webforge\Console\Command as BaseCommand;
use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInteraction;
use Webforge\Common\System\System;

/**
 * Adapter from Webforge CLI Commands to Webforge\Console\Command\CommandAdapter
 */
class WebforgeCommandAdapter extends \Webforge\Console\Command\CommandAdapter {

  protected $cliCommand;

  public function __construct($cliName, ContainerCommand $cliCommand) {
    parent::__construct($cliName, $cliCommand->getContainer()->getSystemContainer()->getSystem());
    $this->cliCommand = $cliCommand;
  }

  public function doExecute(CommandInput $input, CommandOutput $output, CommandInteraction $interact, System $system) {
    $this->cliCommand->initIO($input, $output, $interact, $system);
    
    return $this->cliCommand->executeCLI($input, $output, $interact, $system);
  }
}
