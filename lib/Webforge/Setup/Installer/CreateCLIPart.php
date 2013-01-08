<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;

class CreateCLIPart extends ContainerAwarePart {

  public function __construct() {
    parent::__construct('CreateCLI');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $tpl = function($name) use ($installer) {
      return $installer->getInstallTemplates()->getFile($name);
    };
    
    $bin = $installer->createDir('bin/');
    $lib = $installer->createDir('lib/');    
    
    $installer->copy($tpl('cli.template.php'), $bin->getFile('cli.php'), Installer::IF_NOT_EXISTS);
    $installer->copy($tpl('cli.template.bat'), $bin->getFile('cli.bat'), Installer::IF_NOT_EXISTS);
    $installer->copy($tpl('inc.commands.template.php'), $lib->getFile('inc.commands.php'), Installer::IF_NOT_EXISTS);
  }
}
?>