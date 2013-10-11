<?php

namespace Webforge\Setup;

class BootContainerTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\BootContainer';
    parent::setUp();

    require_once $this->getPackageDir('vendor/pscheit/psc-cms-boot/lib/')->getFile('package.boot.php');
    $this->boot = new \Psc\Boot\BootLoader((string) $GLOBALS['env']['root'], $this->chainClass);

    $this->container = $this->boot->getContainer();
  }

  public function testBootConstructsTheContainer() {
    $this->assertInstanceOf($this->chainClass, $this->boot->getContainer());
  }

  public function testReturnsAWebforgeContainer() {
    $this->assertInstanceOf('Webforge\Framework\Container', $this->container->getWebforge());
  }

  public function testGetsTheLocalPackageFromContainer() {
    $this->assertInstanceOf('Webforge\Framework\Package\Package', $package = $this->container->getPackage());
    $this->assertEquals('webforge/webforge', $package->getIdentifier());
  }

  public function testReturnsTheHostConfig() {
    $this->assertInstanceOf('Webforge\Configuration\Configuration', $this->container->getHostConfiguration());
    $this->assertInstanceOf('Webforge\Configuration\Configuration', $this->container->getHostConfig());
  }
}
