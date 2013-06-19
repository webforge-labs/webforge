<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Framework\Package\PackageAware;
use Webforge\Code\Generator\GProperty;

class CMSContainerPart extends Part implements PackageAware {
  
  public function __construct() {
    parent::__construct('CMSContainer');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $namespace = $this->package->getNamespace();

    $installer->createClass('CMS\Container', Installer::IF_NOT_EXISTS)
      ->parent('Psc\CMS\Roles\AbstractContainer')
      ->withGClass(function ($gClass) use ($installer, $namespace) {
        $gClass->addProperty(
          GProperty::create(
            'defaultNamespace', 
            'String', 
            $installer->ask('Specify the default ControllerNamespace:', $namespace.'\\Controllers')
          )
        );
      })
    ;
  }
}
