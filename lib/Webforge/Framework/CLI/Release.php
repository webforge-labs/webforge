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
    );
  }
  
  public function getDescription() {
    return 'Release with RMT';
  }
  
  public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact) {
    $package = $this->container->getLocalPackage();

    if (!defined('RMT_ROOT_DIR')) {
      define('RMT_ROOT_DIR', $package->getRootDirectory()->getPath(Dir::WITHOUT_TRAILINGSLASH));
    }

    try {
      $rmt = $this->container->getVendorPackage('liip/rmt');

      return $this->executeRelease($rmt);

    } catch (VendorPackageInitException $e) {
      if ($interact->confirm('liip/rmt (composer package) is not installed for this project. Do you want to install RMT now?', TRUE)) {
        $output->msg('Installing liip/rmt with composer (might take a while)');

        if ($this->installRMT($package, $interact)) {
          return $this->executeRelease($this->container->getVendorPackage('liip/rmt'));
        }
      }
    }

    $output->warn('Cannot continue without RMT installed.');
    return 1;
  }

  protected function executeRelease($rmt) {
    return require $rmt->getRootDirectory()->getFile('command.php');
  }

  protected function installRMT($package, $interact) {
    $ret = $this->system->passthru('composer require --dev liip/rmt 0.9.*');

    if ($ret === 0) {
      $config = (object) array(
        "vcs"=>"git",

        "version-generator"=>"semantic",
        "version-persister"=>"vcs-tag",

        "prerequisites"=>array("working-copy-check", "display-last-changes"),
        "post-release-actions"=>array("composer-update", "vcs-publish")
      );

      $config = $package->getRootDirectory()->getFile('rmt.json');
      if (!$config->exists()) {
        $config->writeContents(
          JSONConverter::create()->stringify($config, JSONConverter::PRETTY_PRINT)
        );
      }

      return TRUE;
    }

    return FALSE;
  }
}
