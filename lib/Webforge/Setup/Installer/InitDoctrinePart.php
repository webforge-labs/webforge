<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Framework\ContainerAware;
use Webforge\Code\Generator\GClass;

/**
 * @TODO add bootstrap module to bootstrap.php
 * @TODO add ORM-Commands to the cli application (do this together with the compiler command)
 */
class InitDoctrinePart extends Part implements ContainerAware {
  
  public function __construct() {
    parent::__construct('InitDoctrine');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $db = $installer->createDir('resources/db/');

    $installer->writeTemplate(
      $installer->getInstallTemplates()->getFile('db/001_create_db.sql'),
      $file = $db->getFile('001_create_dbs.sql'),
      $this->readFromConfig()
    );

    $installer->info(
      sprintf(
        "You can create your database with importing %s into your database.",
        $file
      )
    );

    $installer->addCLICommand(new GClass('Psc\System\Console\ORMSchemaCommand'));
    $installer->addCLICommand(new GClass('Psc\System\Console\ORMCreateEntityCommand'));
    $installer->addCLICommand(new GClass('Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand'));
  }

  protected function readFromConfig() {
    $config = $this->container->getLocalProject()->getConfiguration();

    return array(
      'db.name'=>$config->get('db.default.database'),
      'db.user'=>$config->get('db.default.user'),
      'db.password'=>$config->get('db.default.password')
    );
  }
}
