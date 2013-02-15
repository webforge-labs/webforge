<?php

namespace Webforge\Console;

use Symfony\Component\Console\Application;
use Webforge\Common\System\File;
use Webforge\Common\System\Dir;
use Webforge\Framework\Package\Package;
use Psc\System\Console\CommandsIncluder;

class IncludeCommandsConsole {
  
  /**
   * @var Symfony\Component\Console\Application
   */
  protected $application;
  
  public function __construct(Application $application = NULL) {
    $this->application = $application ?: new Application();
  }
  
  /**
   * Adds commands from a commands-includer-file
   * @chainable
   */
  public function includeCommandsFromFile(File $file) {
    $includer = new CommandsIncluder($file);
    $this->application->addCommands($includer->getCommands());
    return $this;
  }
  
  /**
   * @chainable
   */
  public function setName($name) {
    $this->application->setName($name);
    return $this;
  }
  
  /**
   * @chainable
   */
  public function setVersion($version) {
    $this->application->setVersion($version);
    return $this;
  }
}
?>