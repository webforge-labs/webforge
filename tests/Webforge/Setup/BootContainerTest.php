<?php

namespace Webforge\Setup;

use Psc\Boot\BootLoader;
use org\bovigo\vfs\vfsStream;
use Webforge\Common\System\Dir;

class BootContainerTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\BootContainer';
    parent::setUp();

    require_once $this->getPackageDir('vendor/pscheit/psc-cms-boot/lib/')->getFile('package.boot.php');
    $this->boot = new BootLoader((string) $GLOBALS['env']['root'], $this->chainClass);

    $this->container = $this->boot->getContainer();
  }

  public function testBootConstructsTheContainer() {
    $this->assertInstanceOf($this->chainClass, $this->boot->getContainer());
  }

  public function testReturnsAWebforgeContainer() {
    $this->assertInstanceOf('Webforge\Framework\Container', $this->container->getWebforge());
  }

  public function testBootLoaderCanHaveDirAndPassItToBootContainer() {
    $this->assertInstanceOf('Webforge\Common\System\Dir', $GLOBALS['env']['root']);
    $container = new BootContainer($GLOBALS['env']['root']);
  }

  public function testGetsTheLocalPackageFromContainer() {
    $this->assertInstanceOf('Webforge\Framework\Package\Package', $package = $this->container->getPackage());
    $this->assertEquals('webforge/webforge', $package->getIdentifier());
  }

  public function testBootContainerDealsWithNotAddedDirectoriesToTheRegistry() {
    $dir = vfsStream::setup($name = 'not-registered-package');
    vfsStream::copyFromFileSystem((string) $this->getTestDirectory('packages/ACMELibrary/'), $dir, 4098);
    $dir = new Dir(vfsStream::url($name).'/');

    $boot = new BootLoader($dir, $this->chainClass);
    
    $this->assertInstanceOf($this->chainClass, $container = $boot->getContainer());
    $this->assertEquals('acme/library', $container->getPackage()->getIdentifier());
  }


  public function testReturnsTheHostConfig() {
    $this->assertInstanceOf('Webforge\Configuration\Configuration', $this->container->getHostConfiguration());
    $this->assertInstanceOf('Webforge\Configuration\Configuration', $this->container->getHostConfig());
  }

  public function testBootContainerReturnsTheProjectFromProjectsFactory() {
    $this->assertInstanceOf('Webforge\Framework\Project', $project = $this->container->getProject());
    $this->assertEquals('webforge', $project->getLowerName());
  }
}
