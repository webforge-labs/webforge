<?php

namespace Webforge\Setup\Installer;

use Psc\System\Dir;
use Webforge\Setup\Installer\Installer;

class PscJSBoilerplatePart extends Part {
  
  public function __construct() {
    parent::__construct('PscJSBoilerplate');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $profile = 'web';
    $www = $target->sub('www/');
    $js = $www->sub('js/')->create();
    
    $jsTemplates = $installer->getInstallTemplates()->sub('psc-cms-js');
    
    $installer->copy($jsTemplates->getFile('config.js'), $js, Installer::IF_NOT_EXISTS);
    $installer->copy($jsTemplates->getFile($profile.'.main.js'), $js->getFile('main.js'), Installer::IF_NOT_EXISTS);
    $installer->copy($jsTemplates->getFile('boot.js'), $js, Installer::IF_NOT_EXISTS);
    $installer->copy($jsTemplates->getFile('html5.js'), $js->getFile('html5-ie-fix.js'), Installer::IF_NOT_EXISTS);
  }
}
?>