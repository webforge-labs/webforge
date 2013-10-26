<?php

namespace Webforge\Framework\Package;

use Webforge\Framework\Package\Registry;
use Webforge\Framework\Container;
use org\bovigo\vfs\vfsStream;
use Webforge\Common\System\Dir;

class PackagesTestCase extends \Webforge\Code\Test\Base {

  protected $package, $appPackage, $withoutAutoLoadPackage, $oldStylePackage, $camelCasePackage, $underscorePackage, $configPackage, $deployInfoPackage;

  protected $container, $registry;

  public function setUp() {    
    parent::setUp();
    $this->container = new Container();
    $this->registry = new Registry();

    $this->package = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACMELibrary/'));
    $this->withoutAutoLoadPackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/WithoutAutoLoad/'));
    $this->appPackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACME/'));
    $this->oldStylePackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/PscOldStyleProject/Umsetzung/base/src/'));
    $this->camelCasePackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/CoMun/Umsetzung/base/src/'));
    $this->deployInfoPackage = $this->underscorePackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/serien-loader/'));
    $this->configPackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACMESuperBlog/'));
  }

  protected function injectRegistry(Registry $newRegistry = NULL) {
    $this->container->setPackageRegistry($registry = $newRegistry ?: $this->registry);

    return $registry;
  }

  protected function createVirtualPackage($packageNameInPackages) {
    $virtualDirectory = $this->getVirtualDirectoryFromPhysical(
      $packageNameInPackages, 
      $this->getTestDirectory()->sub('packages/'.$packageNameInPackages.'/')
    );

    $vpackage = $this->registry->addComposerPackageFromDirectory($virtualDirectory);

    return $vpackage;
  }

  protected function injectVirtualPackage($packageNameInPackages) {
    $this->container->setLocalPackage($vpackage = $this->createVirtualPackage($packageNameInPackages));

    return $vpackage;
  }

  protected function getVirtualDirectoryFromPhysical($name, Dir $physicalDirectory, $maxFileSize = 2048) {
    $dir = vfsStream::setup($name);

    vfsStream::copyFromFileSystem((string) $physicalDirectory, $dir, $maxFileSize);

    return new Dir(vfsStream::url($name).'/');
  }
}