<?php

namespace Webforge\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Webforge\Common\System\System;

class Command extends SymfonyCommand {

  protected $input;

  protected $output;

  protected $system;

  protected $interactionHelper;

  public function __construct($cliName, System $system) {
    parent::__construct($cliName);
    $this->system = $system;
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->interactionHelper = new InteractionHelper($this->getHelper('dialog'), $output);
    $this->output = new SymfonyCommandOutputAdapter($output);
    $this->input = new SymfonyCommandInputAdapter($input);

    $this->doExecute($this->input, $this->output, $this->interactionHelper, $this->system);
  }
}
