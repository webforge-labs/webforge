<?php

namespace Webforge\Console;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

class InteractionHelper implements CommandInteraction {

  protected $dialog;

  protected $output;

  public function __construct(DialogHelper $dialog, OutputInterface $output) {
    $this->output = $output;
    $this->dialog = $dialog;
  }

  public function ask($question, $default = NULL) {
    return $this->dialog->ask(
      $this->output,
      rtrim($question, ' ').' ',
      $default
    );
  }

  public function askDefault($question, $default) {
    return $this->ask(sprintf($question.' (default %s): ', $default), $default);
  }
  
  public function confirm($question, $default = TRUE) {
    return $this->dialog->askConfirmation(
      $this->output,
      rtrim($question).' ',
      $default
    );
  }
  
  public function askAndValidate($question, \Closure $validator, $attempts = FALSE) {
    return $this->dialog->askAndValidate(
      $this->output,
      rtrim($question, ' ').' ',
      $validator,
      $attempts
    );
  }
}
