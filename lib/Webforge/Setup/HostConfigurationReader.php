<?php

namespace Webforge\Setup;

use Webforge\Common\System\Dir;
use Webforge\Configuration\ConfigurationReader;
use Webforge\Configuration\MissingConfigVariableException;

class HostConfigurationReader {

  protected $reader;

  protected $webforgeStorage;

  public function __construct(ConfigurationReader $reader, ApplicationStorage $webforgeStorage) {
    $this->reader = $reader;
    $this->webforgeStorage = $webforgeStorage;
  }

  public function read() {
    $config = $this->readConfig($from);

    try {
      $config->req('host');
    } catch (MissingConfigVariableException $e) {
      $e->appendMessage('The host configuration was read from: '.$from);
    }

    return $config;
  }

  protected function readConfig(&$from) {
    if ($config = $this->readFromWebforge()) {
      $from = 'webforge application storage';
      return $config;
    } elseif ($config = $this->readFromPsc()) {
      $from = 'psc config dir (PSC_CMS variable pointing to a directory)';
      return $config;
    } else {
      $from = 'default';
      return $this->defaultConfiguration();
    }
  }

  protected function defaultConfiguration() {
    return $this->reader->fromArray(array(
      'host'=>isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : php_uname('n'),
      'development'=>FALSE
    ));
  }

  protected function readFromPsc() {
    $root = getenv('PSC_CMS');
    $hostConfig = FALSE;

    if (!empty($root)) {
      $root = Dir::factoryTS($root);

      $hostConfigFile = $root->getFile('host-config.php');

      if ($hostConfigFile->exists()) {
        $hostConfig = $this->reader->fromPHPFile($hostConfigFile);
      }
    }

    return $hostConfig;
  }

  protected function readFromWebforge() {
    $phpHostFile = $this->webforgeStorage->getFile('host-config.php');

    if ($phpHostFile->exists()) {
      return $this->reader->fromPHPFile($phpHostFile);
    }
  }
}
