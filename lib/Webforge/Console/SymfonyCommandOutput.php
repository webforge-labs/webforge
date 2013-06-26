<?php

namespace Webforge\Console;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Adapter for Symfony Output to the Weboforge\Console\CommandOutput Interface
 */
class SymfonyCommandOutput implements CommandOutput {

  protected $consoleOutput;

  public function __construct(OutputInterface $output) {
    $this->consoleOutput = $output;
  }

  public function ok($msg) {
    return $this->out('<info>'.$msg.'</info>');
  }

  public function out($msg) {
    $this->consoleOutput->writeln($msg);
  }

  /*
  public function warn($msg) {
    return $this->out('<error>'.$msg.'</error>');
  }

  public function info($msg) {
    return $this->out('<info>'.$msg.'</info>');
  }

  public function comment($msg) {
    return $this->out('<comment>'.$msg.'</comment>');
  }
  */
}
