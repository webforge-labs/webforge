<?php

namespace Webforge\Framework\CLI;

use Webforge\Console\Command as BaseCommand;
use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\InteractionHelper;
use Webforge\Common\System\ExecutionSystem;

/**
 * Adapter for symfony console commands with CLI-Commands
 */
class SymfonyCommand extends \Webforge\Console\Command {

  protected $cliCommand;

  public function __construct($cliName, ContainerCommand $cliCommand) {
    parent::__construct($cliName, $cliCommand->getContainer()->getSystemContainer()->getSystem());
    $this->cliCommand = $cliCommand;
  }

  protected function doExecute(CommandInput $input, CommandOutput $output, InteractionHelper $interact, ExecutionSystem $system) {
    $this->cliCommand->initIO($input, $output, $interact, $system);
    
    return $this->cliCommand->executeCLI($input, $output, $interact, $system);
  }
}
