<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Framework\ContainerAware;
use Webforge\Framework\Package\PackageAware;
use Webforge\Framework\Package\Package;
use Webforge\Framework\Container;

abstract class PackageAwarePart extends Part implements PackageAware {

  /**
   * @var Webforge\Framework\Package\Package
   */
  protected $package;

  // @codeCoverageIgnoreStart
  public function setPackage(Package $package) {
    $this->package = $package;
    return $this;
  }
  
  public function getPackage() {
    return $this->package;
  }
  // @codeCoverageIgnoreEnd
}
?>