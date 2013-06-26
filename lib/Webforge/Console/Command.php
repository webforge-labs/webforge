<?php

namespace Webforge\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class Command extends SymfonyCommand {

  protected $input;

  protected $output;

  protected $interactionHelper;

  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->interactionHelper = new InteractionHelper($this->getHelper('dialog'), $output);
    $this->output = new SymfonyCommandOutput($output);
    $this->input = new SymfonyCommandInput($input);

    $this->doExecute($this->input, $this->output, $this->interactionHelper);
  }
}
