<?php

namespace Webforge\Framework\CLI;

use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInteraction;

class Info extends ContainerCommand {
  
  public function defineArguments(Array $api) {
    return array();
  }
  
  public function getDescription() {
    return "Shows some information about webforge, the current package and the environment";
  }
  
  public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact) {
    $storage = $this->container->getApplicationStorage();
    $hostConfig = $storage->getFile('host-config.php');

    $output->ok('Webforge info:');
    $output->msg('');

    if ($hostConfig->exists()) {
      $hostCfg = $this->container->getHostConfiguration();
      $output->msg('host-config was loaded from: '.$hostConfig->resolvePath());
      $output->msg('  defined host: '.$hostCfg->get('host'));
      $output->msg('  developer mode: '.($hostCfg->get('development') ? 'On' : 'Off'));

    } else {
      $output->warn('no host-config was loaded. Place the host-config.php.dist as host-config.php into: '.$storage->getDirectory().' ');
    }
    
    $output->msg('');

    if ($package = $this->container->getLocalPackage()) {
      $output->msg('the local package is: '.$package->getIdentifier());
    } else {
      $output->msg('NOTICE: no local package is defined. This is okay but maybe you forgot to webforge register-package ?');
    }

    $output->msg('');
    $packages = $this->container->getApplicationStorage()->getFile('packages.json');
    if ($packages->exists()) {
      $output->msg(sprintf('You\'re package.json is located in %s and you have %d packages defined.', $packages, count($this->container->getPackageRegistry()->getPackages())));
    } else {
      $output->warn('your packages.json is not findable. Maybe you never called webforge register-package yet?');
    }

    return 0;
  }
}
