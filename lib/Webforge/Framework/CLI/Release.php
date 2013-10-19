<?php

namespace Webforge\Framework\CLI;

use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInteraction;
use Webforge\Common\System\Dir;
use Webforge\Framework\VendorPackageInitException;
use Webforge\Common\JS\JSONConverter;

class Release extends ContainerCommand {
  
  public function defineArguments(Array $api) {
    extract($api);

    return array(
      $arg('do')
    );
  }
  
  public function getDescription() {
    return 'Release with RMT';
  }
  
  public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact) {
    $package = $this->container->getLocalPackage();
    $do = $input->getValue('do');

    try {
      $rmt = $this->container->getVendorPackage('liip/rmt');

      if ($do === 'current') {
        return $this->runCurrent();
      } else {
        return $this->runRelease();
      }

    } catch (VendorPackageInitException $e) {
      if ($interact->confirm('liip/rmt (composer package) is not installed for this project. Do you want to install RMT now?', TRUE)) {
        $output->msg('Installing liip/rmt with composer (might take a while)');

        if ($this->installRMT($package, $interact)) {
          $output->msg('Commit the composer.json and then run webforge release again');
          return 0;
        }
      }
    }

    $output->warn('Cannot continue without RMT installed.');
    return 1;
  }

  protected function runRelease() {
    return $this->container->getReleaseManager()->run(); // runs release per default
  }

  protected function runCurrent() {
    $input = new \Symfony\Component\Console\Input\ArrayInput(array('command'=>'current'));
    return $this->container->getReleaseManager()->run($input);
  }

  protected function installRMT($package, $interact) {
    $ret = $this->system->passthru('composer require --dev liip/rmt 0.9.*');

    if ($ret === 0) {
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

    return FALSE;
  }
}
