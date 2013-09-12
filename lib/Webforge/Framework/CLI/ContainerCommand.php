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
  }

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
}
