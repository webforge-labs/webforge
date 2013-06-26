<?php

namespace Webforge\Framework\CLI;

use Webforge\Common\System\Dir;
use InvalidArgumentException;
use Webforge\Common\JS\JSONConverter;

use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInteraction;

/**
 * Registers the Package found (with $type) in $location in the registry
 */
class RegisterPackage extends ContainerCommand {

  public function getDescription() {
    return 'Registers a local package to be noticed by webforge';
  }

  public function defineArguments(Array $api) {
    extract($api);

    return array(
      $arg('location', 'the path to the location of the product (relatives are resolved relative to current work directory)'),
      $arg('type', 'the type for the packageReader (only composer, yet)', FALSE)
    );
  }

  public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact) {
    return $this->execute(
      $location = $input->getDirectory('location', CommandInput::MUST_EXIST),
      $type = $input->getEnum('type', array('composer'), 'composer')
    );
  }

  /**
   * 
   * its pre assumed that $location is existing
   */
  public function execute(Dir $location, $type = 'composer') {

    if ($type === 'composer') {
      $package = $this->container->getPackageRegistry()->addComposerPackageFromDirectory($location);
      $this->output->ok(sprintf("Found ComposerPackage: '%s'", $package->getSlug()));
      
      // write to packages.json in appDir
      $packagesFile = $this->container->getApplicationStorage()->getFile('packages.json');
      $jsonConverter = new JSONConverter();
      
      // read
      $packages = new \stdClass;
      if ($packagesFile->exists()) {
        $packages = $jsonConverter->parseFile($packagesFile);
      }
      
      // modify
      $packages->{$package->getSlug()} = (string) $package->getRootDirectory();      
      
      // write
      $packagesFile->writeContents($jsonConverter->stringify($packages, JSONConverter::PRETTY_PRINT));
      $this->output->ok('updated packages-registry in: '.$packagesFile);

    // @codeCoverageIgnoreStart
    // is always checked through getEnum
    } else {
      throw new InvalidArgumentException(sprintf("Type '%s' is not defined", $type));
    }
    // @codeCoverageIgnoreEnd
  }

}
