<?php

namespace Webforge\Framework\CLI;

use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInteraction;
use Webforge\Common\System\Dir;
use Webforge\Framework\VendorPackageInitException;
use Webforge\Common\JS\JSONConverter;
use InvalidArgumentException;

class Release extends ContainerCommand {
  
  public function defineArguments(Array $api) {
    extract($api);

    return array(
      $arg('do', 'What to do default: release can be current to show the release', $required = FALSE)
    );
  }
  
  public function getDescription() {
    return 'Release with RMT';
  }
  
  public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact) {
    $package = $this->container->getLocalPackage();
    $do = $input->getValue('do');

    $releaseManager = $this->container->getReleaseManager();

    // rmt does not expose release as command if rmt is not initialized (json does not exist)
    try {
      $releaseCommand = $releaseManager->find('release');
    } catch (InvalidArgumentException $e) {

      if ($interact->confirm('liip/rmt config file is not found for this project. Do you want to install RMT now?', TRUE)) {

        if ($this->installRMT($package, $interact)) {
          $output->msg('Wrote a default rmt.json. Add and commit it and then run webforge release again');
          return 0;
        }
      }

      $output->warn('Cannot continue without RMT installed.');
      return 1;
    }

    if ($do === 'current') {
      return $this->runCurrent($releaseManager);
    } else {
      return $this->runRelease($releaseManager);
    }
  }

  protected function runRelease($releaseManager) {
    return $releaseManager->run(); // runs release per default
  }

  protected function runCurrent($releaseManager) {
    $input = new \Symfony\Component\Console\Input\ArrayInput(array('command'=>'current'));
    return $releaseManager->run($input);
  }

  protected function installRMT($package, $interact) {
    $rmtConfig = (object) array(
      "vcs"=>"git",

      "version-generator"=>"semantic",
      "version-persister"=>"vcs-tag",

      "prerequisites"=>array("working-copy-check", "display-last-changes"),
      "pre-release-actions"=>array("composer-update", "vcs-commit"),
      "post-release-actions"=>array("vcs-publish")
    );

    $config = $package->getRootDirectory()->getFile('rmt.json');
    if (!$config->exists()) {
      $config->writeContents(
        JSONConverter::create()->stringify($rmtConfig, JSONConverter::PRETTY_PRINT)
      );
    }

    return TRUE;
  }
}
