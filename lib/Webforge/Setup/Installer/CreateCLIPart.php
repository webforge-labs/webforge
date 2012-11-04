<?php

namespace Webforge\Setup\Installer;

use Psc\System\Dir;

class CreateCLIPart extends ContainerAwarePart {

  public function __construct() {
    parent::__construct('CreateCLI');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $resources = $this->container->getResourceDirectory();
    
    $tpl = function($name) use ($resources) {
      return $resources->sub('installTemplates/')->getFile($name);
    };
    
    $bin = $target->sub('bin/')->create();
    $lib = $target->sub('lib/')->create();
    
    $installer->copy($tpl('cli.template.php'), $bin->getFile('cli.php'), Installer::IF_NOT_EXISTS);
    $installer->copy($tpl('cli.template.bat'), $bin->getFile('cli.bat'), Installer::IF_NOT_EXISTS);
    $installer->copy($tpl('inc.commands.template.php'), $lib->getFile('inc.commands.php'), Installer::IF_NOT_EXISTS);
  }
}
?>