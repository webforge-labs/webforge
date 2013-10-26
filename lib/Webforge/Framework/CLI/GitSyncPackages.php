<?php

namespace Webforge\Framework\CLI;

use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInteraction;

class GitSyncPackages extends ContainerCommand {
  
  public function defineArguments(Array $api) {
    extract($api);

    return array();
  }

  public function getCLIName() {
    return 'git:sync-packages';
  }
  
  public function getDescription() {
    return 'Loops through all your packages and executes git pull with fast forward';
  }
  
  public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact) {
    foreach ($this->container->getPackageRegistry()->getPackages() as $package) {
      $output->msg('Pulling '.$package->getIdentifier());

      $this->system->setWorkingDirectory($package->getRootDirectory());
      $this->system->passthru('git pull --ff-only origin');
      $output->msg('');
      $output->msg('');
    }
  }
}
