<?php

namespace Webforge\Framework\Package;

use Webforge\Framework\Package\Registry;
use Webforge\Framework\Container;

class PackagesTestCase extends \Webforge\Code\Test\Base {

  protected $registry, $package, $appPackage, $withoutAutoLoadPackage, $oldStylePackage, $camelCasePackage, $underscorePackage, $configPackage;

  public function setUp() {
    parent::setUp();

    $this->registry = new Registry();
    $this->package = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACMELibrary/'));
    $this->withoutAutoLoadPackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/WithoutAutoLoad/'));
    $this->appPackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACME/'));
    $this->oldStylePackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/PscOldStyleProject/Umsetzung/base/src/'));
    $this->camelCasePackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/CoMun/Umsetzung/base/src/'));
    $this->underscorePackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/serien-loader/'));
    $this->configPackage = $this->registry->addComposerPackageFromDirectory($this->getTestDirectory()->sub('packages/ACMESuperBlog/'));
  }

  protected function injectRegistry() {
    $container = new Container();
    $container->setPackageRegistry($this->registry);

    return $container;
  }
}