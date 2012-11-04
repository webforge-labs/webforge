<?php

namespace Webforge\Setup\Installer;

use Psc\System\File;
use Psc\System\Dir;

class TestPart extends Part {
  
  protected $testFile;
  
  public function __construct(File $testFile) {
    parent::__construct('test');
    $this->testFile = $testFile;
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $installer->copy($this->testFile, $target->getFile('testing.php'));
  }
}
?>