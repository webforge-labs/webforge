<?php

namespace Webforge\Framework\CLI;

use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInteraction;

use Webforge\Code\Generator\ClassCreater;
use Webforge\Code\Generator\GFunctionBody;
use Webforge\Code\Generator\GClass;

class InstallPart extends ContainerCommand {

  const OVERWRITE = ClassCreater::OVERWRITE;

  public function getCLIName() {
    return 'install:part';
  }
  
  public function defineArguments(Array $api) {
    extract($api);

    $location = $arg('location', 'the path to the location of the product (relatives are resolved relative to current work directory). If not set the current work directory is used', FALSE);
    $location->setDefault('.'); // grr not chainable

    return array(
      $arg('part', 'the name of the part. You can see a list of part names in install:list-parts', FALSE),
      $location
    );
  }

  public function getDescription() {
    return 'Installs a part in the current project. Parts are a small snippets without many options.';
  }
  
  public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact) {
    $partName = $input->getValue('part');
    $location = $input->getDirectory('location');

    $partsInstaller = $this->container->getPartsInstaller($interact, $output);

    if (empty($partName)) {
      $output->msg('parts avaible:');
      foreach ($partsInstaller->getParts() as $part) {
        $output->msg('  '.$part->getName());
      }

      $partName = $interact->ask('Which part do you want to install?');

      if (empty($partName)) {
        return 1;
      }
    }

    $part = $partsInstaller->getPart($partName);
    $output->msg('installing '.$part->getName());
    
    $partsInstaller->install($part, $location);

    return 0;
  }
}
