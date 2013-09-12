<?php

namespace Webforge\Framework\CLI;

use Webforge\Common\System\Dir;
use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInteraction;
use Webforge\Common\Preg;
use Webforge\Common\JS\JSONConverter;
use Webforge\Common\System\Util as SystemUtil;

class Init extends ContainerCommand {

  protected $root;

  public function getDescription() {
    return 'Registers a local package to be noticed by webforge';
  }

  public function defineArguments(Array $api) {
    extract($api);

    return array(
      $arg('root', 'the path to the root of the new package (relatives are resolved relative to current work directory)')
      //$arg('type', 'the type for the packageReader (only composer, yet)', FALSE)
    );
  }

  public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact) {
    return $this->execute(
      $input->getDirectory('root', CommandInput::MUST_EXIST)
      //$input->getEnum('type', array('composer'), 'composer'),
    );
  }

  /**
   * 
   * its pre assumed that $root is existing
   */
  public function execute(Dir $root) {
    $this->root = $root;

    list($vendor, $packageSlug) = $this->retrieveVendorAndSlug();

    if ($this->interact->confirm('Do you want to init a composer configuration?', TRUE)) {
      $params = array(
        'working-dir'=>(string) $this->root,
        'no-interaction'=>NULL,
        'name'=>$vendor.'/'.$packageSlug,
        "description"=>$this->retrieveDescription(),
        'stability'=>$this->retrieveMinimumStability()
      );

      $exit = $this->system->exec($cmd = 'composer init '.$this->buildParams($params), function($type, $out) {
        print $out;
      });

      if ($exit !== 0) {
        throw new \RuntimeException('Cannot run the composer init command: '."\n".$cmd);
      }


      // needs to be written after composer init
      $configs = array();
      $this->retrieveExtraConfig($configs);

      foreach ($configs as $key => $value) {
        $this->system->exec(
          sprintf('composer config %s %s %s',
            $this->buildParams(array('working-dir'=>(string) $this->root)),
            $key,
            $this->systemEsc($value)
          )
        );
      }

    } else {
      $this->output->warn('I cannot init webforge successfully, because the package cannot be read (only composer repositories yet)');
    }

    return 0;
  }

  protected function retrieveVendorAndSlug() {
    $defaultVendor = $defaultSlug = NULL;

    if (mb_strpos($this->root->getName(), '-') !== FALSE) {
      list($defaultVendor, $defaultSlug) = explode('-', $this->root->getName(), 2);
    }

    $vendor = $this->interact->askDefault('What is your vendor name (lowercase)?', $defaultVendor);
    $slug = $this->interact->askDefault('What is the name of your package (lowercase)?', $defaultSlug);

    return array($vendor, $slug);
  }

  protected function retrieveDescription() {
    $readme = $this->root->getFile('README.md');
    if ($readme->exists()) {
      if ($desc = Preg::qmatch($readme->getContents(), '/.+[\r\n]+\=+[\r\n]+(?:\s*[\r\n]*)(.+)[\s\r\n]*$/')) {
        return $desc;
      }
    }

    return '';
  }

  protected function retrieveMinimumStability() {
    return 'dev';
  }

  protected function retrieveExtraConfig(Array &$configs) {
    $configs['extra.branch-alias.dev-master'] = '1.0.x-dev';
  }

  protected function systemEsc($string) {
    return SystemUtil::escapeShellArg($string, $this->system->getOperatingSystem());
  }

  protected function buildParams(Array $params) {
    $str = '';
    foreach ($params as $param=>$value) {
      if ($value === NULL) {
        $str .= sprintf('--%s ', $param);
      } else {
        $str .= sprintf('--%s=%s ', $param, $this->systemEsc($value));
      }
    }

    return mb_substr($str, 0, -1);
  }
}
