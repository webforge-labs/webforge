<?php

namespace Webforge\Framework\CLI;

use Webforge\Framework\Container;
use Webforge\Common\ClassUtil;
use Webforge\Console\StringCommandOutput;
use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInput;
use Webforge\Console\CommandInteraction;
use Webforge\Common\System\ExecutionSystem;

abstract class ContainerCommand {

  /**
   * @var Webforge\Framework\Container
   */
  protected $container;

  protected $output, $input, $interact, $system;

  public function __construct(Container $container) {
    $this->container = $container;
    $this->output = new StringCommandOutput();
    $this->setUp();
  }

  protected function setUp() {
  }

  /**
   * $arg = function ($name, $description = NULL, $required = TRUE, $multiple = FALSE) // default: required
   * $opt = function($name, $short = NULL, $withValue = TRUE, $description = NULL) // default: mit value required
   * $defOpt = function($name, $short = NULL, $default = NULL, $description = NULL) // with value and a default value
   * $flag = function($name, $short = NULL, $description) // ohne value
   */
  abstract public function defineArguments(Array $api);

  abstract public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact);

  /**
   * Returns a lower dashed name for the console api
   */
  public function getCLIName() {
    return $this->container->getInflector()->commandNameit(
      ClassUtil::getClassName(get_class($this))
    );
  }

  /**
   * 
   * this is always called before execute()
   */
  public function initIO(CommandInput $input, CommandOutput $output, CommandInteraction $interact, ExecutionSystem $system) {
    $this->output = $output;
    $this->input = $input;
    $this->interact = $interact;
    $this->system = $system;
  }

  /**
   * @return Webforge\Framework\Container
   */
  public function getContainer() {
    return $this->container;
  }
}
