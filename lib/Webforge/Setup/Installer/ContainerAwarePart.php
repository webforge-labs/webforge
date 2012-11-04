<?php

namespace Webforge\Setup\Installer;

use Psc\System\Dir;
use Webforge\Framework\ContainerAware;
use Webforge\Framework\Container;

abstract class ContainerAwarePart extends Part implements ContainerAware {

  /**
   * @var Webforge\Framework\Container
   */
  protected $container;
  
  public function setContainer(Container $container) {
    $this->container = $container;
    return $this;
  }
}
?>