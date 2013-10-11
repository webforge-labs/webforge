<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Common\String as S;
use Webforge\Framework\Package\Package;

class InstallTestSuitePart extends ContainerAwarePart implements \Webforge\Framework\Package\PackageAware {

  /**
   * @var Webforge\Framework\Package\Package
   */
  protected $package;

  protected $testplateVersion = '1.*';
  
  protected $installPHPUnitLocally = FALSE;

  public function __construct() {
    parent::__construct('InstallTestSuite');
    $this->installPHPUnitLocally = FALSE;
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $tpl = function($name) use ($installer) {
      return $installer->getInstallTemplates()->getFile($name);
    };
    
    $installer->write(
      S::miniTemplate(
        $tpl('phpunit.template.xml')->getContents(),
        array('packageTitle'=>$this->package->getTitle())
      ),
      $target->getFile('phpunit.xml.dist'),
      Installer::IF_NOT_EXISTS
    );
    
    $tests = $installer->createDir('tests');
    
    if ($target->getFile('bootstrap.php')) {
      $installer->warn('bootstrap.php should exist for php unit tests bootstrapping');
    }
    
    // add testplate
    $installer->info('adding webforge-testplate with composer (that might take a while) ...');
    $installer->execute(
      sprintf('composer --working-dir=%s --dev require webforge/testplate:'.$this->testplateVersion, $target->getQuotedString())
    );
    
    if ($this->installPHPUnitLocally) {
      $installer->execute(
        sprintf('composer --working-dir=%s --dev require phpunit/phpunit:3.7.x-dev', $target->getQuotedString())
      );
    }
  }
}
